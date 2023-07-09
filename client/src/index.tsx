import React from "react";
import {render} from "react-dom";
import {
    BrowserRouter as Router,
    Routes,
    Route
} from "react-router-dom";
import {store} from '../store';
import {Provider} from 'react-redux';
import '../styles/globals.scss';
import '../styles/nprogress.scss';
import Home from "../pages";
import Profile from "../pages/profile";
import Literature from "../pages/literature";
import Users from "../pages/users";
import Term from "../pages/term";
import Page404 from "../pages/404";
// import MyTask from  "../pages/tasks/tasksTeacher";
import Leaders from "../pages/users/leaders"
import CreateTask from "../pages/tasks/createTask";
import Tasks from "../pages/tasks/Tasks";
import Task from "../pages/tasks/Task";
import EditTask from "../pages/tasks/editTask";
import TaskPlay from "../pages/tasks/TaskGame";
import SimpleToast from "../components/Toast/SimpleToast";

import Educations from "../pages/educations"
import Education from "../pages/educations/[eduId]"

import StatsTask from "../pages/tasks/stats/[taskId]"
import SSE from "../components/SSE/SSE";

const App = () => {
    return (
        <>
            <Provider store={store}>
                <Router>
                    <Routes>
                        <Route path="/">
                            <Route path="404" element={<Page404/>}/>
                            <Route index element={<Home/>}/>
                            <Route path="profile" element={<Profile/>}/>
                            <Route path="materials">
                                <Route path="literature" element={<Literature/>}/>
                                <Route path="terms" element={<Term/>}/>
                            </Route>
                            <Route path="users">
                                <Route path="allUsers" element={<Users/>}/>
                                <Route path="leaders" element={<Leaders/>}/>
                            </Route>
                            <Route path="tasks">
                                <Route path="all" element={<Tasks/>}/>
                                <Route path="createTask" element={<CreateTask/>}/>
                                <Route path="task" element={<Task/>}/>
                                <Route path="editTask" element={<EditTask/>}/>
                                <Route path="taskPlay" element={<TaskPlay/>}/>

                                <Route path="stats">
                                    <Route path=":taskId" element={<StatsTask/>}/>
                                </Route>
                            </Route>
                            <Route path="educations">
                                <Route index element={<Educations/>}/>
                                <Route path=":eduId" element={<Education/>}/>
                            </Route>
                        </Route>
                        <Route path="*" element={<Page404/>}/>
                    </Routes>
                </Router>
                <SSE/>
                <SimpleToast/>
            </Provider>
        </>
    );
}

render(<App/>, document.querySelector("#root"));
