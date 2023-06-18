import {createAsyncThunk, createSlice} from "@reduxjs/toolkit";
import {getRequest} from "../../api";
import {Topics} from "../../models/response/topic";


export const getTopicsInfo = createAsyncThunk<Promise<Topics[] | { error: any }>>(
    '/topicsInfo/getTopicsInfo',
    async () => getRequest('/topics')
);


interface TopicsSelfState {
    info: {
        data: Topics,
        isLoading: boolean,
        error: string | null
    },
}

const initialState: TopicsSelfState = {
    info: {
        data: null,
        isLoading: false,
        error: null
    },
};

export const topicsInfoSlice = createSlice({
    name: 'topicsInfo',
    initialState,
    reducers: {},
    extraReducers: (builder) => {
        builder
            .addCase(getTopicsInfo.pending, (state) => {
                state.info = {
                    data: null,
                    isLoading: true,
                    error: null
                }
            })
            .addCase(getTopicsInfo.fulfilled, (state, action) => {
                state.info = {
                    ...state.info,
                    ...action.payload,
                    isLoading: false
                }
            })
            .addCase(getTopicsInfo.rejected, (state, action) => {
                state.info = {
                    data: null,
                    isLoading: false,
                    error: action.error.message
                }
            })
    }
});

export default topicsInfoSlice.reducer;