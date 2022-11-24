import classNames from 'classnames';
import React from 'react';
import s from './VerticalMenu.module.scss';

export default function VerticalMenu({ header, position, children, className, ...props }) {
    const borderStyle = 
        position === 'right' 
        ? { borderLeft: '1px solid #262626' } 
        : position === 'left' 
        ? { borderRight: '1px solid #262626' } 
        : {};

    return(
        <div {...props} className={classNames(s.container, className)} style={borderStyle}>
            { header && <span className={s.header} key={'header'}>{header}</span> }
            { children }
        </div>
    );
}