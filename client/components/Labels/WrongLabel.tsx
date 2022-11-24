import classNames from 'classnames';
import React, {FC} from 'react';
import s from './.module.scss';

interface IWrongLabelProps {
    className?: string,
    onClick?: () => void
}

const WrongLabel: FC<IWrongLabelProps> = ({className, onClick}) => {
    return (
        <span
            className={classNames(s.wrong, className)}
            onClick={onClick}
            title={'Ответ неверен'}
        >Неверно</span>
    );
}

export default WrongLabel;