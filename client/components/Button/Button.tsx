import React, {FC} from 'react';
import classNames from "classnames";
import s from './Button.module.scss';

interface IProps {
    className?: string,
    type?: 'button' | 'submit' | 'reset',
    style?: any,
    onClick?: () => void,
    disabled?: boolean,
    title?: string
}

const Button: FC<IProps> = ({children, className, type, style, onClick, disabled, title}) => {
    return (
        <button
            title={title}
            disabled={disabled}
            onClick={onClick}
            style={style}
            type={type}
            className={classNames(s.button, className)}
        >{children}</button>
    );
};

export default Button;