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
import CreatTask from  "../pages/tasks/creatTask";
import Tasks from  "../pages/tasks/Tasks";
import Task from  "../pages/tasks/Task";
import EditTask from  "../pages/tasks/editTask";
import TaskPlay from  "../pages/tasks/TaskGame";
import MyTask from  "../pages/tasks/tasksTeacher";
import Toast from "../components/Toast/Toast";

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
                            <Route path="users" element={<Users/>}/>
                            <Route path="tasks/creatTask" element={<CreatTask/>}/>
                            <Route path="tasks/allTasks" element={<Tasks/>}/>
                            <Route path="tasks/task" element={<Task/>}/>
                            <Route path="tasks/editTask" element={<EditTask/>}/>
                            <Route path="tasks/taskPlay" element={<TaskPlay/>}/>
                            <Route path="tasks/myTasks" element={<MyTask/>}/>
                        </Route>
                        <Route path="*" element={<Page404 />} />
                    </Routes>
                </Router>
                {/*<Toast />*/}
            </Provider>
        </>
    );
}

render(<App/>, document.querySelector("#root"));
