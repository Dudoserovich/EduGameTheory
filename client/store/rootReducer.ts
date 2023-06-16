import { combineReducers } from "redux";
import newUserSlice from "./slices/newUserSlice";
import authSlice from "./slices/authSlice";
import userInfoSlice from "./slices/userSlice";
import generalSlice from "./slices/generalSlice";
import taskSlice from "./slices/taskSolveSlice";
import literatureSlice from "./slices/literatureSlice";
import termSlice from "./slices/termSlice";
import topicSlice from "./slices/topicSlice";

export const rootReducer = combineReducers({
    newUser: newUserSlice,
    user: userInfoSlice,
    auth: authSlice,
    task: taskSlice,
    general: generalSlice,
    literature: literatureSlice,
    term: termSlice,
    topic: topicSlice
});