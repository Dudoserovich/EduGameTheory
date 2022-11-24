import classNames from 'classnames';
import React, {FC} from 'react';
import s from './.module.scss';

interface ICorrectLabelProps {
    className?: string,
    onClick?: () => void
}

const CorrectLabel: FC<ICorrectLabelProps> = ({className, onClick}) => {
    return (
        <span
            className={classNames(s.correct, className)}
            onClick={onClick}
            title={'Ответ верен'}
        >Верно</span>
    );
}

export default CorrectLabel;