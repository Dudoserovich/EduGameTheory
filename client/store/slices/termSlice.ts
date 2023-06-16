import {createAsyncThunk, createSlice} from "@reduxjs/toolkit";
import {getRequest, putRequest} from "../../api";


export const getTerms = createAsyncThunk<Promise<string | { error: any }>>(
    '/terms/getTerms',
    async () => getRequest('/terms')
);

interface IData {
    id: number;
    name: string;
    description: string;
    topic: {id: string, name: string};
}

interface ITermsSlice {
    info: {
        data: IData,
        isLoading: boolean,
        error: string | null
    }
}

const initialState: ITermsSlice = {
    info: {
        data: null,
        isLoading: false,
        error: null
    }
};

export const termSlice = createSlice({
    name: 'terms',
    initialState,
    reducers: {},
    extraReducers: (builder) => {
        builder
            .addCase(getTerms.pending, (state) => {
                state.info = {
                    data: null,
                    isLoading: true,
                    error: null
                }
            })
            .addCase(getTerms.fulfilled, (state, action) => {
                state.info = {
                    ...state.info,
                    ...action.payload,
                    isLoading: false
                }
            })
            .addCase(getTerms.rejected, (state, action) => {
                state.info = {
                    data: null,
                    isLoading: false,
                    error: action.error.message
                }
            })
    }
});

export default termSlice.reducer;