import React, { useState } from "react";
import classNames from "classnames";
import s from './Dropdown.module.scss';
import AnimateHeight from "react-animate-height";

export default function Dropdown({ dropdownClass, float = 'right', openOnClick = false, value, children, ...props }) {
    const [isOpen, setIsOpen] = useState(false);
    const stateChangeType = 
        openOnClick 
        ?
        { onClick: () => setIsOpen(isOpen => !isOpen) } 
        :
        { onMouseEnter: () => setIsOpen(true), onMouseLeave: () => setIsOpen(false) }

    return (
        <div {...props} {...stateChangeType}>
            {value}
            <div {...props} className={classNames(s.container, dropdownClass)} style={{ [float]: 0 }}>
                <AnimateHeight height={isOpen ? 'auto' : 0} >
                    {children}
                </AnimateHeight>
            </div>
        </div>
        
    )
}