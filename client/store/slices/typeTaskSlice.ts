import {createAsyncThunk, createSlice} from "@reduxjs/toolkit";
import {getRequest} from "../../api";


export const getStrategyInfo = createAsyncThunk<Promise<String[] | { error: any }>>(
    '/strategyInfo/getStrategyInfo',
    async () => getRequest('/tasks/payoff/strategy')
);

interface StrategySelfState {
    info: {
        data: String[],
        isLoading: boolean,
        error: string | null
    },
}

const initialState: StrategySelfState = {
    info: {
        data: null,
        isLoading: false,
        error: null
    },
};

export const strategyInfoSlice = createSlice({
    name: 'strategyInfo',
    initialState,
    reducers: {},
    extraReducers: (builder) => {
        builder
            .addCase(getStrategyInfo.pending, (state) => {
                state.info = {
                    data: null,
                    isLoading: true,
                    error: null
                }
            })
            .addCase(getStrategyInfo.fulfilled, (state, action) => {
                state.info = {
                    ...state.info,
                    ...action.payload,
                    isLoading: false
                }
            })
            .addCase(getStrategyInfo.rejected, (state, action) => {
                state.info = {
                    data: null,
                    isLoading: false,
                    error: action.error.message
                }
            })
    }
});

export default strategyInfoSlice.reducer;