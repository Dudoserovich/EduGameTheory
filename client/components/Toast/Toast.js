import React, {useEffect} from 'react';
import {toast, ToastContainer} from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';

const Toast = () => {
    useEffect(() => {
        let origin = window.location.protocol + '//' + window.location.host

        const url = new URL(`${origin}/.well-known/mercure`);
        url.searchParams.append('topic', '/news');

        const eventSource = new EventSource(url);

        eventSource.onopen = function() {
            console.log('Connection SSE opened');
        };

        eventSource.addEventListener('news', event => {
            let message = JSON.parse(event.data).message
            console.log(event)
            notify(message)
        })

        // eventSource.onmessage = e => console.log(e); // do something with the payload
        // eventSource.onmessage = e => {
        //     let message = JSON.parse(e.data).message
        //     console.log(e)
        //     notify(message)
        // }

    }, [])

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