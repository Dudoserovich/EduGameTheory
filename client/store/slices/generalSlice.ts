import {createAsyncThunk, createSlice} from "@reduxjs/toolkit";
import {getRequest} from "../../api";
import {IUser, IUser1} from "../../models/response/user";


export const getUsers = createAsyncThunk<Promise<IUser[] | { error: any }>>(
    '/general/getUsers',
    async () => getRequest('/users')
);

export const getTeacherUsers = createAsyncThunk<Promise<IUser1[] | { error: any }>>(
    '/userInfo/getTeacherUsers',
    async () => getRequest('/users/teachers')
);

interface IGeneralState {
    users: {
        data: [],
        isLoading: boolean,
        error: string | null
    },
    teachers: {
        data: [],
        isLoading: boolean,
        error: string | null
    }
}

const initialState: IGeneralState = {
    users: {
        data: [],
        isLoading: false,
        error: null
    },
    teachers: {
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

            .addCase(getTeacherUsers.pending, (state) => {
                state.teachers = {
                    data: [],
                    isLoading: true,
                    error: null
                }
            })
            .addCase(getTeacherUsers.fulfilled, (state, action) => {
                state.teachers = {
                    ...state.teachers,
                    ...action.payload,
                    isLoading: false
                }
            })
            .addCase(getTeacherUsers.rejected, (state, action) => {
                state.teachers = {
                    data: [],
                    isLoading: false,
                    error: action.error.message
                }
            })
    }
});

export default generalSlice.reducer;