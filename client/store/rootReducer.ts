import { combineReducers } from "redux";
import newUserSlice from "./slices/newUserSlice";
import authSlice from "./slices/authSlice";
import userInfoSlice from "./slices/userSlice";
import generalSlice from "./slices/generalSlice";
import literatureSlice from "./slices/literatureSlice";
import termSlice from "./slices/termSlice";
import taskSlice from "./slices/taskSolveSlice";
import tasksInfoSlice from "./slices/tasksSlice";
import topicsInfoSlice from "./slices/topicSlice";
import newTaskSlice from "./slices/creatTaskSlice";
import strategyInfoSlice from "./slices/typeTaskSlice";
import tasksGameSlice from "./slices/taskGameSlice";
import playInfoSlice from "./slices/taskPlaySlice";
import tasksPayoffSlice from "./slices/taskPayoffSlice";
import playGameSlice from "./slices/taskPlayGameSlice";
import tasksTeacherInfoSlice from "./slices/teacherTasksSlice";
import getAchievementsSlice from "./slices/achivSlice";
import educationSlice from "./slices/educationSlice";
import taskStatsSlice from "./slices/taskStats";

export const rootReducer = combineReducers({
    achievements: getAchievementsSlice,
    newUser: newUserSlice,
    strategy: strategyInfoSlice,
    user: userInfoSlice,
    auth: authSlice,
    task: taskSlice,
    tasksTeacher: tasksTeacherInfoSlice,
    taskPayoff: tasksPayoffSlice,
    taskGame: tasksGameSlice,
    taskPlay: playInfoSlice,
    playGame: playGameSlice,
    general: generalSlice,
    literature: literatureSlice,
    term: termSlice,
    tasks: tasksInfoSlice,
    newTask: newTaskSlice,
    topics: topicsInfoSlice,
    education: educationSlice,
    taskStatsSlice: taskStatsSlice
});