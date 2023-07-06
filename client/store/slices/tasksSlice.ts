import {createAsyncThunk, createSlice} from "@reduxjs/toolkit";
import {deleteRequest, getRequest, putRequest} from "../../api";
import {AllTasks} from "../../models/response/task";


export const getTasksInfo = createAsyncThunk<Promise<AllTasks[] | { error: any }>>(
    '/tasksInfo/getTasksInfo',
    async () => getRequest('/tasks')
);

export const updateTaskInfo = createAsyncThunk<Promise<{ code: number } | { error: any }>,{id, IData} >(
    '/tasksInfo/updateTaskInfo',
    async (data) => putRequest(`/tasks/${data.id} `, data.IData)
);

export const deleteTask = createAsyncThunk<Promise<{ code: number } | { error: any }>, number >(
    '/tasksInfo/deleteTask',
    async taskId => deleteRequest(`/tasks/${taskId} `)
);

interface TasksSelfState {
    info: {
        data: AllTasks,
        isLoading: boolean,
        error: string | null
    },
    updateInfo: {
        data: {status: number, success: string},
        isLoading: boolean,
        error: string | null
    },
    deleteInfo: {
        data: {status: number, success: string},
        isLoading: boolean,
        error: string | null
    }
}

const initialState: TasksSelfState = {
    info: {
        data: null,
        isLoading: false,
        error: null
    },
    updateInfo: {
        data: null,
        isLoading: false,
        error: null
    },
    deleteInfo: {
        data: null,
        isLoading: false,
        error: null
    }
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
                state.updateInfo = {
                    data: null,
                    isLoading: true,
                    error: null
                }
            })
            .addCase(updateTaskInfo.fulfilled, (state, action) => {
                state.updateInfo = {
                    ...state.updateInfo,
                    ...action.payload,
                    isLoading: false
                }
            })
            .addCase(updateTaskInfo.rejected, (state, action) => {
                state.updateInfo = {
                    data: null,
                    isLoading: false,
                    error: action.error.message
                }
            })

            .addCase(deleteTask.pending, (state) => {
                state.deleteInfo = {
                    data: null,
                    isLoading: true,
                    error: null
                }
            })
            .addCase(deleteTask.fulfilled, (state, action) => {
                state.deleteInfo = {
                    ...state.deleteInfo,
                    ...action.payload,
                    isLoading: false
                }
            })
            .addCase(deleteTask.rejected, (state, action) => {
                state.deleteInfo = {
                    data: null,
                    isLoading: false,
                    error: action.error.message
                }
            })
    }
});

export default tasksInfoSlice.reducer;