import {createAsyncThunk, createSlice} from "@reduxjs/toolkit";
import {refreshingToken, testPostRequest} from "../../api";
import {IUser} from "../../models/response/user";


export const createUser = createAsyncThunk<Promise<{ code: number } | { error: any }>, IUser, { rejectValue: string }>(
    '/newUser/createUser',
    async (data, {rejectWithValue}) => {
        try {
            const response = await testPostRequest('/users', data);
            return response.data;
        } catch (err) {
            if (err.response.data.code === 401) {
                await refreshingToken();
                return createUser(data);
            } else {
                return rejectWithValue(err.response.data);
            }
        }
    }
);

export const testCreateUser = createAsyncThunk<Promise<{ code: number } | { error: any }>, IUser, { rejectValue: string }>(
    '/newUser/testCreateUser',
    async (data , {rejectWithValue}) => {
        try {
            const response = await testPostRequest('/users', data);
            return response.data;
        } catch (err) {
            return rejectWithValue(err.response.data.code);
        }
    }
);

interface ICreateUserState {
    creatingUser: {
        data: {
            status: number,
            success: string
        },
        isLoading: boolean,
        error: string | null
    }
}

const initialState: ICreateUserState = {
    creatingUser: {
        data: null,
        isLoading: false,
        error: null
    }
};

export const newUserSlice = createSlice({
    name: 'newUser',
    initialState,
    reducers: {},
    extraReducers: (builder) => {
        builder
            .addCase(createUser.pending, (state) => {
                state.creatingUser = {
                    data: null,
                    isLoading: true,
                    error: null
                }
            })
            .addCase(createUser.fulfilled, (state, action) => {
                state.creatingUser = {
                    ...state.creatingUser,
                    ...action.payload,
                    isLoading: false
                }
            })
            .addCase(createUser.rejected, (state, action) => {
                state.creatingUser = {
                    data: null,
                    isLoading: false,
                    error: action.error.message
                }
            })
    }
});

export default newUserSlice.reducer;