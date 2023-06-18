import { combineReducers } from "redux";
import newUserSlice from "./slices/newUserSlice";
import authSlice from "./slices/authSlice";
import userInfoSlice from "./slices/userSlice";
import generalSlice from "./slices/generalSlice";
import taskSlice, {taskSolveSlice} from "./slices/taskSolveSlice";
import tasksInfoSlice from "./slices/tasksSlice";
import topicsInfoSlice from "./slices/topicSlice";
import newTaskSlice from "./slices/creatTaskSlice";

export const rootReducer = combineReducers({
    newUser: newUserSlice,
    user: userInfoSlice,
    auth: authSlice,
    task: taskSlice,
    tasks: tasksInfoSlice,
    newTask: newTaskSlice,
    topics: topicsInfoSlice,
    general: generalSlice
});