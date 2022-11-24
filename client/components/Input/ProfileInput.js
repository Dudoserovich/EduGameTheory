import React, { forwardRef } from 'react';
import classNames from 'classnames';
import s from './profileInput.module.scss';

const ProfileInput = forwardRef((props, ref) => (
    <div className={classNames(s.content, props.className)}>
        { props.label ? <label className={s.label} htmlFor={props.id}>{props.label}</label> : <></>}
        <input {...props} ref={ref} className={s.field}/>
    </div>
));

export default ProfileInput;