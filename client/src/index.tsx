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
import Page404 from "../pages/404";

const App = () => {
    return (
        <Provider store={store}>
            <Router>
                <Routes>
                    <Route path="/">
                        <Route path="404" element={<Page404/>}/>
                        <Route index element={<Home/>}/>
                        <Route path="profile" element={<Profile/>}/>
                    </Route>
                    <Route path="*" element={<Page404 />} />
                </Routes>
            </Router>
        </Provider>
    );
}

render(<App/>, document.querySelector("#root"));
