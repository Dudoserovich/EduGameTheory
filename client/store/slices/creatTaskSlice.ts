import {createAsyncThunk, createSlice} from "@reduxjs/toolkit";
import {postRequest, putRequest} from "../../api";



export const createTask = createAsyncThunk<Promise<{ code: number } | { error: any }>, {ITask}>(
    '/newTask/createTask',
    async (data) => await postRequest('/tasks', data.ITask)
);

export const checkMatrixInfo = createAsyncThunk<Promise<{ code: number } | { error: any }>, object>(
    '/newTask/matrixInfo',
    async (data) => await putRequest('/teacher/tasks/matrixInfo', data)
);


interface ICreateTaskState {
    creatingTask: {
        data: {
            status: number,
            success: string
        },
        isLoading: boolean,
        error: string | null
    },
    matrixInfo: {
        data: {
            status: number,
            success: string
        }
        isLoading: boolean,
        error: string | null
    }
}

const initialState: ICreateTaskState = {
    creatingTask: {
        data: null,
        isLoading: false,
        error: null
    },
    matrixInfo: {
        data: null,
        isLoading: false,
        error: null
    }
};

export const newTaskSlice = createSlice({
    name: 'newTask',
    initialState,
    reducers: {},
    extraReducers: (builder) => {
        builder
            .addCase(createTask.pending, (state) => {
                state.creatingTask = {
                    data: null,
                    isLoading: true,
                    error: null
                }
            })
            .addCase(createTask.fulfilled, (state, action) => {
                state.creatingTask = {
                    ...state.creatingTask,
                    ...action.payload,
                    isLoading: false
                }
            })
            .addCase(createTask.rejected, (state, action) => {
                state.creatingTask = {
                    data: null,
                    isLoading: false,
                    error: action.error.message
                }
            })

            .addCase(checkMatrixInfo.pending, (state) => {
                state.matrixInfo = {
                    data: null,
                    isLoading: true,
                    error: null
                }
            })
            .addCase(checkMatrixInfo.fulfilled, (state, action) => {
                state.matrixInfo = {
                    ...state.matrixInfo,
                    ...action.payload,
                    isLoading: false
                }
            })
            .addCase(checkMatrixInfo.rejected, (state, action) => {
                state.matrixInfo = {
                    data: null,
                    isLoading: false,
                    error: action.error.message
                }
            })
    }
});

export default newTaskSlice.reducer;