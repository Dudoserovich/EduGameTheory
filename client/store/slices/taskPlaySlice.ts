import {createAsyncThunk, createSlice} from "@reduxjs/toolkit";
import {getRequest, putRequest} from "../../api";
import {TasksPlay} from "../../models/response/task";


export const getPlayInfo = createAsyncThunk<Promise<TasksPlay | { error: any }>, {id} >(
    '/playInfo/getPlayInfo',
    async (data) => getRequest(`/tasks/${data.id}/turns/play`)
);

export const restartTaskPayoff = createAsyncThunk<Promise<{ code: number } | { error: any }>,{id, ITaskPayoff} >(
    '/playInfo/restartTaskPayoff',
    async (data) => putRequest(`/tasks/${data.id}/turns/restart `, data.ITaskPayoff)
);
interface TasksSelfState {
    info: {
        data: TasksPlay,
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

export const playInfoSlice = createSlice({
    name: 'playInfo',
    initialState,
    reducers: {},
    extraReducers: (builder) => {
        builder
            .addCase(getPlayInfo.pending, (state) => {
                state.info = {
                    data: null,
                    isLoading: true,
                    error: null
                }
            })
            .addCase(getPlayInfo.fulfilled, (state, action) => {
                state.info = {
                    ...state.info,
                    ...action.payload,
                    isLoading: false
                }
            })
            .addCase(getPlayInfo.rejected, (state, action) => {
                state.info = {
                    data: null,
                    isLoading: false,
                    error: action.error.message
                }
            })
    }
});

export default playInfoSlice.reducer;