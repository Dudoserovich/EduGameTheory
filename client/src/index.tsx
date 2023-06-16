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
                        </Route>
                        <Route path="*" element={<Page404 />} />
                    </Routes>
                </Router>
                <Toast />
            </Provider>
        </>
    );
}

render(<App/>, document.querySelector("#root"));
