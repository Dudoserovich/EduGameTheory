import {createAsyncThunk, createSlice} from "@reduxjs/toolkit";
import {getRequest, putRequest} from "../../api";


export const getTopics = createAsyncThunk<Promise<string | { error: any }>>(
    '/terms/getTopics',
    async () => getRequest('/topics')
);

interface IData {
    id: number;
    name: string;
}

interface ITopicsSlice {
    info: {
        data: IData,
        isLoading: boolean,
        error: string | null
    }
}

const initialState: ITopicsSlice = {
    info: {
        data: null,
        isLoading: false,
        error: null
    }
};

export const topicSlice = createSlice({
    name: 'topics',
    initialState,
    reducers: {},
    extraReducers: (builder) => {
        builder
            .addCase(getTopics.pending, (state) => {
                state.info = {
                    data: null,
                    isLoading: true,
                    error: null
                }
            })
            .addCase(getTopics.fulfilled, (state, action) => {
                state.info = {
                    ...state.info,
                    ...action.payload,
                    isLoading: false
                }
            })
            .addCase(getTopics.rejected, (state, action) => {
                state.info = {
                    data: null,
                    isLoading: false,
                    error: action.error.message
                }
            })
    }
});

export default topicSlice.reducer;