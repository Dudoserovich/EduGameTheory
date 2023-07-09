import {useSSE} from "react-hooks-sse";
import {getJWT} from "../../scripts/jwtService";
import jwtDecode from "jwt-decode";
import React from "react";
import {notify} from "../Toast/SimpleToast";

const Events = () => {
    const news = useSSE('news', {
        message: null,
    });
    notify(news.message)

    const token = getJWT()
    if (token) {
        const decodedToken = jwtDecode(token)
        const event = useSSE(decodedToken?.id.toString(), {
            message: null,
        });

        notify(event.message)
    }

    return (
        <></>
    );
};

export default Events;