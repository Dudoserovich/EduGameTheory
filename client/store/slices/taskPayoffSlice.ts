import {createAsyncThunk, createSlice} from "@reduxjs/toolkit";
import { putRequest} from "../../api";


export const TaskPayoff = createAsyncThunk<Promise<{ code: number } | { error: any }>,{id, ITaskPayoff} >(
    '/tasksInfo/TaskPayoff',
    async (data) => putRequest(`/tasks/${data.id}/solve/payoff `, data.ITaskPayoff)
);


interface IData {
    success: boolean;
    message: string;
}
interface TasksSelfState {
    info: {
        data: IData,
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

export const tasksPayoffSlice = createSlice({
    name: 'tasksInfo',
    initialState,
    reducers: {},
    extraReducers: (builder) => {
        builder
            .addCase(TaskPayoff.pending, (state) => {
                state.info = {
                    data: null,
                    isLoading: true,
                    error: null
                }
            })
            .addCase(TaskPayoff.fulfilled, (state, action) => {
                state.info = {
                    ...state.info,
                    ...action.payload,
                    isLoading: false
                }
            })
            .addCase(TaskPayoff.rejected, (state, action) => {
                state.info = {
                    data: null,
                    isLoading: false,
                    error: action.error.message
                }
            })
    }
});

export default tasksPayoffSlice.reducer;