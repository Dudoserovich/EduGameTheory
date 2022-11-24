import classNames from 'classnames';
import React, {FC} from 'react';
import s from './.module.scss';

interface IWaitingLabelProps {
    className?: string
    onClick?: () => void
}

const WaitingLabel: FC<IWaitingLabelProps> = ({className, onClick}) => {
    return (
        <span
            className={classNames(s.waiting, className)}
            onClick={onClick}
            title={'Ожидание ответа'}
        >Ожидание</span>
    );
}

export default WaitingLabel;