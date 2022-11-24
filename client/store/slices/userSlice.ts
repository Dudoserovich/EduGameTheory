import {createAsyncThunk, createSlice} from "@reduxjs/toolkit";
import {getRequest, putRequest} from "../../api";
import {IUser} from "../../models/response/user";


export const getUserInfo = createAsyncThunk<Promise<IUser[] | { error: any }>>(
    '/userInfo/getUserInfo',
    async () => getRequest('/users/self')
);

interface IData {
    login: string;
    old_password: string;
    new_password: string;
    full_name: string;
    email: string;
}

export const updateUserInfo = createAsyncThunk<Promise<{ code: number } | { error: any }>, IData>(
    '/userInfo/updateUserInfo',
    async (data) => putRequest('/users/self', data)
);

interface IUserSelfState {
    info: {
        data: IUser,
        isLoading: boolean,
        error: string | null
    }
}

const initialState: IUserSelfState = {
    info: {
        data: null,
        isLoading: false,
        error: null
    }
};

export const userInfoSlice = createSlice({
    name: 'userInfo',
    initialState,
    reducers: {},
    extraReducers: (builder) => {
        builder
            .addCase(getUserInfo.pending, (state) => {
                state.info = {
                    data: null,
                    isLoading: true,
                    error: null
                }
            })
            .addCase(getUserInfo.fulfilled, (state, action) => {
                state.info = {
                    ...state.info,
                    ...action.payload,
                    isLoading: false
                }
            })
            .addCase(getUserInfo.rejected, (state, action) => {
                state.info = {
                    data: null,
                    isLoading: false,
                    error: action.error.message
                }
            })
    }
});

export default userInfoSlice.reducer;