import { combineReducers } from "redux";
import newUserSlice from "./slices/newUserSlice";
import authSlice from "./slices/authSlice";
import userInfoSlice from "./slices/userSlice";
import generalSlice from "./slices/generalSlice";
import taskSlice, {taskSolveSlice} from "./slices/taskSolveSlice";

export const rootReducer = combineReducers({
    newUser: newUserSlice,
    user: userInfoSlice,
    auth: authSlice,
    task: taskSlice,
    general: generalSlice
});