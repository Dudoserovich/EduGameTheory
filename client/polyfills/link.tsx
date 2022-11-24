import React, {FC} from "react";
import {Link as BaseLink} from "react-router-dom";

const Link: FC<{ href: string, key?: string }> = ({href, children, ...props}) => {
    return React.createElement(BaseLink, {to: href, ...props}, children);
};

export default Link;
