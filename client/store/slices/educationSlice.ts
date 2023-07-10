import {createAsyncThunk, createSlice} from "@reduxjs/toolkit";
import {getRequest, putRequest} from "../../api";
import {Task} from "../../models/response/task";


export const getEducations = createAsyncThunk<Promise<{ data: ISimpleEdu[] }  | { error: any }>>(
    '/education/getEducations',
    async () => getRequest('/education')
);

export const getEducation = createAsyncThunk<Promise<{ data: ISimpleEdu } | { error: any }>, number>(
    '/education/getEducation',
    async (id) => getRequest(`/education/${id}/start`)
);

export const getEducationBlocks = createAsyncThunk<Promise<{ data: ISimpleEdu } | { error: any }>, number>(
    '/education/getEducationBlocks',
    async (id) => getRequest(`/education/${id}/blocks`)
);

export const educationAdd = createAsyncThunk<Promise<{ code: number } | { error: any }>,{id, idBloc} >(
    '/education/educationAdd',
    async (data) => putRequest(`/education/${data.id}/${data.idBloc}`)
);

interface IFullEdu {
    id: number;
    education_tasks: object;
    current_block: boolean;
    success: boolean;
}

interface IEduBlocks {
    id: number;
    task: object;
    block_number: number;
    theory_text: string;
}

interface ISimpleEdu {
    id: number;
    name: string;
    description: string;
    topic: {id: number, name: string};
    conclusion: string;
    progress: {passed: number, total: number};
    // image_base64: string;
}

interface IEducationsSlice {
    info: {
        data: ISimpleEdu,
        isLoading: boolean,
        error: string | null
    },
    edu: {
        data: IFullEdu,
        isLoading: boolean,
        error: string | null
    },
    blocks: {
        data: IEduBlocks,
        isLoading: boolean,
        error: string | null
    }
}

const initialState: IEducationsSlice = {
    info: {
        data: null,
        isLoading: false,
        error: null
    },
    edu: {
        data: null,
        isLoading: false,
        error: null
    },
    blocks: {
        data: null,
        isLoading: false,
        error: null
    }
};

export const educationSlice = createSlice({
    name: 'educations',
    initialState,
    reducers: {},
    extraReducers: (builder) => {
        builder
            .addCase(getEducations.pending, (state) => {
                state.info = {
                    data: null,
                    isLoading: true,
                    error: null
                }
            })
            .addCase(getEducations.fulfilled, (state, action) => {
                state.info = {
                    ...state.info,
                    ...action.payload,
                    isLoading: false
                }
            })
            .addCase(getEducations.rejected, (state, action) => {
                state.info = {
                    data: null,
                    isLoading: false,
                    error: action.error.message
                }
            })

            .addCase(getEducation.pending, (state) => {
                state.edu = {
                    data: null,
                    isLoading: true,
                    error: null
                }
            })
            .addCase(getEducation.fulfilled, (state, action) => {
                state.edu = {
                    ...state.edu,
                    ...action.payload,
                    isLoading: false
                }
            })
            .addCase(getEducation.rejected, (state, action) => {
                state.edu = {
                    data: null,
                    isLoading: false,
                    error: action.error.message
                }
            })

            .addCase(getEducationBlocks.pending, (state) => {
                state.blocks = {
                    data: null,
                    isLoading: true,
                    error: null
                }
            })
            .addCase(getEducationBlocks.fulfilled, (state, action) => {
                state.blocks = {
                    ...state.blocks,
                    ...action.payload,
                    isLoading: false
                }
            })
            .addCase(getEducationBlocks.rejected, (state, action) => {
                state.blocks = {
                    data: null,
                    isLoading: false,
                    error: action.error.message
                }
            })
    }
});

export default educationSlice.reducer;