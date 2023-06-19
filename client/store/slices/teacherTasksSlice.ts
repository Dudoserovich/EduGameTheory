import {createAsyncThunk, createSlice} from "@reduxjs/toolkit";
import {getRequest, putRequest} from "../../api";
import {AllTasks} from "../../models/response/task";


export const getTasksTeacherInfo = createAsyncThunk<Promise<AllTasks[] | { error: any }>>(
    '/tasksInfo/getTasksTeacherInfo',
    async () => getRequest('/teacher/tasks')
);

interface TasksSelfState {
    info: {
        data: AllTasks,
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

export const tasksTeacherInfoSlice = createSlice({
    name: 'tasksInfo',
    initialState,
    reducers: {},
    extraReducers: (builder) => {
        builder
            .addCase(getTasksTeacherInfo.pending, (state) => {
                state.info = {
                    data: null,
                    isLoading: true,
                    error: null
                }
            })
            .addCase(getTasksTeacherInfo.fulfilled, (state, action) => {
                state.info = {
                    ...state.info,
                    ...action.payload,
                    isLoading: false
                }
            })
            .addCase(getTasksTeacherInfo.rejected, (state, action) => {
                state.info = {
                    data: null,
                    isLoading: false,
                    error: action.error.message
                }
            })
    }
});

export default tasksTeacherInfoSlice.reducer;