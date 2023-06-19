import {createAsyncThunk, createSlice} from "@reduxjs/toolkit";
import {getRequest} from "../../api";


export const getAchievements = createAsyncThunk<Promise<IData[] | { error: any }>>(
    '/achivInfo/getAchievements',
    async () => getRequest('/achievements/users/self/completed')
);
interface IData {
    id: number;
    achievement: {
        id: number;
        name: string;
        description: string;
        image_href: string;
    };
    progress: {
        current_score: number;
        need_score: number;
    };
    achievement_date: string;

}
interface TasksSelfState {
    info: {
        data: IData[],
        isLoading: boolean,
        error: string | null
    },
}

const initialState: TasksSelfState = {
    info: {
        data: null,
        isLoading: false,
        error: null
    },
};

export const getAchievementsSlice = createSlice({
    name: 'achivInfo',
    initialState,
    reducers: {},
    extraReducers: (builder) => {
        builder
            .addCase(getAchievements.pending, (state) => {
                state.info = {
                    data: null,
                    isLoading: true,
                    error: null
                }
            })
            .addCase(getAchievements.fulfilled, (state, action) => {
                state.info = {
                    ...state.info,
                    ...action.payload,
                    isLoading: false
                }
            })
            .addCase(getAchievements.rejected, (state, action) => {
                state.info = {
                    data: null,
                    isLoading: false,
                    error: action.error.message
                }
            })
    }
});

export default getAchievementsSlice.reducer;