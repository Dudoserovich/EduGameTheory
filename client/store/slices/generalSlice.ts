import {createAsyncThunk, createSlice} from "@reduxjs/toolkit";
import {getRequest} from "../../api";
import {IUser} from "../../models/response/user";


export const getUsers = createAsyncThunk<Promise<IUser[] | { error: any }>>(
    '/general/getUsers',
    async () => getRequest('/users')
);

interface IGeneralState {
    users: {
        data: IUser[],
        isLoading: boolean,
        error: string | null
    }
}

const initialState: IGeneralState = {
    users: {
        data: [],
        isLoading: false,
        error: null
    }
};

export const generalSlice = createSlice({
    name: 'general',
    initialState,
    reducers: {},
    extraReducers: (builder) => {
        builder
            .addCase(getUsers.pending, (state) => {
                state.users = {
                    data: [],
                    isLoading: true,
                    error: null
                }
            })
            .addCase(getUsers.fulfilled, (state, action) => {
                state.users = {
                    ...state.users,
                    ...action.payload,
                    isLoading: false
                }
            })
            .addCase(getUsers.rejected, (state, action) => {
                state.users = {
                    data: [],
                    isLoading: false,
                    error: action.error.message
                }
            })
    }
});

export default generalSlice.reducer;