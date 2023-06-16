import React, {useEffect} from 'react';
import {toast, ToastContainer} from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import {useDispatch, useSelector} from "react-redux";

const Toast = () => {
    const dispatch = useDispatch();
    const user = useSelector(state => state.user.info);

    function getEventSource() {
        let origin = window.location.protocol + '//' + window.location.host

        const url = new URL(`${origin}/.well-known/mercure`);
        url.searchParams.append('topic', '/news');
        url.searchParams.append('topic', '/achievements');

        return new EventSource(url);
    }

    const fetchData = async (type = 'news') => {
        const eventSource = getEventSource();

        eventSource.onopen = function () {
            console.log('Connection SSE opened');
        };

        eventSource.addEventListener(type, event => {
            let message = JSON.parse(event.data).message
            // console.log(event)
            notify(message)
        })

        // eventSource.onmessage = e => console.log(e); // do something with the payload
        // eventSource.onmessage = e => {
        //     let message = JSON.parse(e.data).message
        //     console.log(e)
        //     notify(message)
        // }
    }

    // useEffect(() => {
    //     fetchData()
    // }, [])

    useEffect(() => {
        fetchData(user?.data?.login)
    }, [user.data])

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

export default Toast;
export const notify = (message) => toast(message);