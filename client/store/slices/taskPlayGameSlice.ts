import {createAsyncThunk, createSlice} from "@reduxjs/toolkit";
import {putRequest} from "../../api";


export const TaskPlayPayoff = createAsyncThunk<Promise<{ code: number } | { error: any }>,{id, IData} >(
    '/playInfo/TaskPlayPayoff',
    async (data) => putRequest(`/tasks/${data.id}/turns/play`, data.IData)
);

interface IData {
    moves: number[];
    chance_first: number[];
    chance_second: number[];
    your_chance: number[];
    result_move: number;
    score: number;
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

export const playGameSlice = createSlice({
    name: 'playInfo',
    initialState,
    reducers: {},
    extraReducers: (builder) => {
        builder
            .addCase(TaskPlayPayoff.pending, (state) => {
                state.info = {
                    data: null,
                    isLoading: true,
                    error: null
                }
            })
            .addCase(TaskPlayPayoff.fulfilled, (state, action) => {
                state.info = {
                    ...state.info,
                    ...action.payload,
                    isLoading: false
                }
            })
            .addCase(TaskPlayPayoff.rejected, (state, action) => {
                state.info = {
                    data: null,
                    isLoading: false,
                    error: action.error.message
                }
            })
    }
});

export default playGameSlice.reducer;