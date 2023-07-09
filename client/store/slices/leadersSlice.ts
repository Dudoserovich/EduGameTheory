import {createAsyncThunk, createSlice} from "@reduxjs/toolkit";
import {getRequest} from "../../api";


export const getLeadersInfo = createAsyncThunk<Promise<ILeaders[] | { error: any }>>(
    '/leaders/getLeadersInfo',
    async () => getRequest('/progress/leaders')
);


interface ILeaders {
    current_level: { name: string, need_scores: number },
    next_level: {
    name: string,
    need_scores: number,
        },
    scores: number,
    user: {
            avatar_base64: string,
            avatar_name: string,
            fio: string,
            id: number,
        }
}

interface TasksSelfState {
    info: {
        data: ILeaders[],
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

export const leadersInfoSlice = createSlice({
    name: 'leaders',
    initialState,
    reducers: {},
    extraReducers: (builder) => {
        builder
            .addCase(getLeadersInfo.pending, (state) => {
                state.info = {
                    data: null,
                    isLoading: true,
                    error: null
                }
            })
            .addCase(getLeadersInfo.fulfilled, (state, action) => {
                state.info = {
                    ...state.info,
                    ...action.payload,
                    isLoading: false
                }
            })
            .addCase(getLeadersInfo.rejected, (state, action) => {
                state.info = {
                    data: null,
                    isLoading: false,
                    error: action.error.message
                }
            })
    }
});

export default leadersInfoSlice.reducer;
