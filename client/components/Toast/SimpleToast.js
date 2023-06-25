import React, {useEffect} from 'react';
import {toast, ToastContainer} from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import {useDispatch, useSelector} from "react-redux";

const ToastSSE = () => {

    return (
        <ToastContainer
            position='bottom-right'
            autoClose={10000}
            hideProgressBar={false}
            newestOnTop={false}
            closeOnClick
            rtl={false}
            pauseOnFocusLoss
            draggable
            pauseOnHover
            theme='dark'
        />
    );
}

export default ToastSSE;
export const notify = (message) => toast(message);