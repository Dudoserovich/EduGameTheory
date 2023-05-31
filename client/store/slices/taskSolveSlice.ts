import {createAsyncThunk, createSlice} from "@reduxjs/toolkit";
import {getRequest, putRequest} from "../../api";

interface IData {
    strategy: string;
    first_player: number | number[];
    second_player: number | number[];
    game_price: number;
}

export const solvePayoff = createAsyncThunk<Promise<{ code: number } | { error: any }>, IData>(
    '/task/solvePayoff',
    async (data) => putRequest('/tasks/117/solve/payoff', data)
);

interface ITaskSolvePayoff {
    info: {
        data: {success: boolean, message: string},
        isLoading: boolean,
        error: string | null
    }
}

const initialState: ITaskSolvePayoff = {
    info: {
        data: null,
        isLoading: false,
        error: null
    }
};

export const taskSolveSlice = createSlice({
    name: 'userInfo',
    initialState,
    reducers: {},
    extraReducers: (builder) => {
        builder
            .addCase(solvePayoff.pending, (state) => {
                state.info = {
                    data: null,
                    isLoading: true,
                    error: null
                }
            })
            .addCase(solvePayoff.fulfilled, (state, action) => {
                state.info = {
                    ...state.info,
                    ...action.payload,
                    isLoading: false
                }
            })
            .addCase(solvePayoff.rejected, (state, action) => {
                state.info = {
                    data: null,
                    isLoading: false,
                    error: action.error.message
                }
            })
    }
});

export default taskSolveSlice.reducer;