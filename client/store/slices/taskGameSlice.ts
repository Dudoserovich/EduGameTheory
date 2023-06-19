import {createAsyncThunk, createSlice} from "@reduxjs/toolkit";
import {putRequest} from "../../api";
import {AllTasks} from "../../models/response/task";


export const TaskGame = createAsyncThunk<Promise<{ code: number } | { error: any }>,{id, ITaskPayoff} >(
    '/tasksInfo/TaskGame',
    async (data) => putRequest(`/tasks/${data.id}/solve/risk`, data.ITaskPayoff)
);


interface TasksSelfState {
    info: {
        data: string,
        isLoading: boolean,
        error: string | null
    },
}

const initialState: TasksSelfState = {
    info: {
        data: null,
        isLoading: false,
        error: null
    },
};

export const tasksGameSlice = createSlice({
    name: 'tasksInfo',
    initialState,
    reducers: {},
    extraReducers: (builder) => {
        builder
            .addCase(TaskGame.pending, (state) => {
                state.info = {
                    data: null,
                    isLoading: true,
                    error: null
                }
            })
            .addCase(TaskGame.fulfilled, (state, action) => {
                state.info = {
                    ...state.info,
                    ...action.payload,
                    isLoading: false
                }
            })
            .addCase(TaskGame.rejected, (state, action) => {
                state.info = {
                    data: null,
                    isLoading: false,
                    error: action.error.message
                }
            })
    }
});

export default tasksGameSlice.reducer;