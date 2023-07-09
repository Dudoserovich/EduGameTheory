import {createAsyncThunk, createSlice} from "@reduxjs/toolkit";
import {getRequest} from "../../api";


export const getLevelInfo = createAsyncThunk<Promise<ILevel[] | { error: any }>>(
    '/level/getLevelInfo',
    async () => getRequest('/progress/self')
);


interface ILevel {
    scores: number,
    current_level: {
        name: string,
        need_scores: number
    },
    next_level: {
        name: string,
        need_scores: number
    }
}

interface TasksSelfState {
    info: {
        data: ILevel[],
        isLoading: boolean,
        error: string | null
    },
    updateInfo: {
        data: { status: number, success: string },
        isLoading: boolean,
        error: string | null
    },
    deleteInfo: {
        data: { status: number, success: string },
        isLoading: boolean,
        error: string | null
    }
}

const initialState: TasksSelfState = {
    info: {
        data: null,
        isLoading: false,
        error: null
    },
    updateInfo: {
        data: null,
        isLoading: false,
        error: null
    },
    deleteInfo: {
        data: null,
        isLoading: false,
        error: null
    }
};

export const levelInfoSlice = createSlice({
    name: 'level',
    initialState,
    reducers: {},
    extraReducers: (builder) => {
        builder
            .addCase(getLevelInfo.pending, (state) => {
                state.info = {
                    data: null,
                    isLoading: true,
                    error: null
                }
            })
            .addCase(getLevelInfo.fulfilled, (state, action) => {
                state.info = {
                    ...state.info,
                    ...action.payload,
                    isLoading: false
                }
            })
            .addCase(getLevelInfo.rejected, (state, action) => {
                state.info = {
                    data: null,
                    isLoading: false,
                    error: action.error.message
                }
            })
    }
});

export default levelInfoSlice.reducer;