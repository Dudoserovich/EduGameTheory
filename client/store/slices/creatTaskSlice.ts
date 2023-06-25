import {createAsyncThunk, createSlice} from "@reduxjs/toolkit";
import {postRequest, putRequest, refreshingToken, testPostRequest} from "../../api";
import {IUser} from "../../models/response/user";


export const createTask = createAsyncThunk<Promise<{ code: number } | { error: any }>, IUser>(
    '/newTask/createTask',
    async (data) => await postRequest('/tasks', data)
);


interface ICreateTaskState {
    creatingTask: {
        data: {
            status: number,
            success: string
        },
        isLoading: boolean,
        error: string | null
    }
}

const initialState: ICreateTaskState = {
    creatingTask: {
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
    }
});

export default newTaskSlice.reducer;