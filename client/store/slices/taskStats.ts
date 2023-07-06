import {createAsyncThunk, createSlice} from "@reduxjs/toolkit";
import {deleteRequest, getRequest, putRequest} from "../../api";
import {AllTasks} from "../../models/response/task";

export const getTaskStats = createAsyncThunk<Promise<object | { error: any }>, number>(
    '/tasksInfo/getTaskStats',
    async (taskId) => getRequest(`/teacher/tasks/${taskId}/result`)
);

interface TaskStats {
    info: {
        data: object,
        isLoading: boolean,
        error: string | null
    }
}

const initialState: TaskStats = {
    info: {
        data: null,
        isLoading: false,
        error: null
    }
};

export const taskStatsSlice = createSlice({
    name: 'tasksInfo',
    initialState,
    reducers: {},
    extraReducers: (builder) => {
        builder
            .addCase(getTaskStats.pending, (state) => {
                state.info = {
                    data: null,
                    isLoading: true,
                    error: null
                }
            })
            .addCase(getTaskStats.fulfilled, (state, action) => {
                state.info = {
                    ...state.info,
                    ...action.payload,
                    isLoading: false
                }
            })
            .addCase(getTaskStats.rejected, (state, action) => {
                state.info = {
                    data: null,
                    isLoading: false,
                    error: action.error.message
                }
            })

    }
});

export default taskStatsSlice.reducer;