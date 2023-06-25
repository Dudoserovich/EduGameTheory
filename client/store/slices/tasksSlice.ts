import {createAsyncThunk, createSlice} from "@reduxjs/toolkit";
import {getRequest, putRequest} from "../../api";
import {AllTasks} from "../../models/response/task";


export const getTasksInfo = createAsyncThunk<Promise<AllTasks[] | { error: any }>>(
    '/tasksInfo/getTasksInfo',
    async () => getRequest('/tasks')
);

export const updateTaskInfo = createAsyncThunk<Promise<{ code: number } | { error: any }>,{id, IData} >(
    '/tasksInfo/updateTaskInfo',
    async (data) => putRequest(`/tasks/${data.id} `, data.IData)
);


interface IData {
    name: string;
    description: string;
    matrix: number[][];
    flag_matrix: string;
    topic_id: number;
}
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

export const tasksInfoSlice = createSlice({
    name: 'tasksInfo',
    initialState,
    reducers: {},
    extraReducers: (builder) => {
        builder
            .addCase(getTasksInfo.pending, (state) => {
                state.info = {
                    data: null,
                    isLoading: true,
                    error: null
                }
            })
            .addCase(getTasksInfo.fulfilled, (state, action) => {
                state.info = {
                    ...state.info,
                    ...action.payload,
                    isLoading: false
                }
            })
            .addCase(getTasksInfo.rejected, (state, action) => {
                state.info = {
                    data: null,
                    isLoading: false,
                    error: action.error.message
                }
            })

            .addCase(updateTaskInfo.pending, (state) => {
                state.info = {
                    data: null,
                    isLoading: true,
                    error: null
                }
            })
            .addCase(updateTaskInfo.fulfilled, (state, action) => {
                state.info = {
                    ...state.info,
                    ...action.payload,
                    isLoading: false
                }
            })
            .addCase(updateTaskInfo.rejected, (state, action) => {
                state.info = {
                    data: null,
                    isLoading: false,
                    error: action.error.message
                }
            })
    }
});

export default tasksInfoSlice.reducer;