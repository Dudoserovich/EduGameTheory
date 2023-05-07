import React, {useEffect} from 'react';
import {toast, ToastContainer} from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';

const Toast = () => {
    useEffect(() => {
        let origin = window.location.protocol + '//' + window.location.host
        //let origin = 'http://' + window.location.host
        // The subscriber subscribes to updates for the https://example.com/users/dunglas topic
        const url = new URL(`${origin}/.well-known/mercure`);
        url.searchParams.append('topic', '/news');

        const eventSource = new EventSource(url);
        // The callback will be called every time an update is published
        ``

        eventSource.onopen = function() {
            console.log('Connection SSE opened');
        };

        // eventSource.onmessage = e => console.log(e); // do something with the payload
        eventSource.onmessage = e => {
            let message = JSON.parse(e.data).message
            notify(message)
        }

        eventSource.onerror = function(event) {
            console.error('Error occurred (SSE):', event);

            if (event.target.readyState === EventSource.CLOSED) {
                console.log('Connection SSE closed');
            } else {
                console.log('Attempting to reconnect SSE...');
                setTimeout(() => {
                    eventSource.close();

                    const url = new URL(`${origin}/.well-known/mercure`);
                    url.searchParams.append('topic', '/news');

                    const newEventSource = new EventSource(url);

                    newEventSource.onopen = eventSource.onopen;
                    newEventSource.onmessage = eventSource.onmessage;
                    newEventSource.onerror = eventSource.onerror;
                }, 5000);
            }
        };
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