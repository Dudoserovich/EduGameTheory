import {createAsyncThunk, createSlice} from "@reduxjs/toolkit";
import {getRequest, putRequest} from "../../api";


export const getLiteratures = createAsyncThunk<Promise<string | { error: any }>>(
    '/literatures/getLiteratures',
    async () => getRequest('/literatures')
);

interface IData {
    id: number;
    name: string;
    description: string;
    link: string;
    topic: {id: string, name: string};
    image_base64: string;
}

interface ILiteraturesSlice {
    info: {
        data: IData,
        isLoading: boolean,
        error: string | null
    }
}

const initialState: ILiteraturesSlice = {
    info: {
        data: null,
        isLoading: false,
        error: null
    }
};

export const literatureSlice = createSlice({
    name: 'literatures',
    initialState,
    reducers: {},
    extraReducers: (builder) => {
        builder
            .addCase(getLiteratures.pending, (state) => {
                state.info = {
                    data: null,
                    isLoading: true,
                    error: null
                }
            })
            .addCase(getLiteratures.fulfilled, (state, action) => {
                state.info = {
                    ...state.info,
                    ...action.payload,
                    isLoading: false
                }
            })
            .addCase(getLiteratures.rejected, (state, action) => {
                state.info = {
                    data: null,
                    isLoading: false,
                    error: action.error.message
                }
            })
    }
});

export default literatureSlice.reducer;