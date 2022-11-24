import React from 'react';
import s from './Checkbox.module.scss';

export default function Checkbox({isCheck, onChange, ...props}) {

    return (
        <input {...props}
            className={s.ctn} 
            type={'checkbox'}
            checked={isCheck}
            onChange={onChange}
            />
    );
}