import React from 'react';
import {SSEProvider} from 'react-hooks-sse';
import Events from './Events';

const SSE = () => {
    let origin = window.location.protocol + '//' + window.location.host

    const url = new URL(`${origin}/.well-known/mercure`);
    const topics = [
        '/news',
        '/achievements',
        '/tasks'
    ]

    topics.forEach((topic) =>
        url.searchParams.append('topic', topic))

    const urlStr = url.toString().slice(origin.length);

    return (
        <SSEProvider endpoint={urlStr}>
            <Events/>
        </SSEProvider>
    );
}

export default SSE;
