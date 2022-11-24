import classNames from 'classnames';
import React, {FC} from 'react';
import s from './.module.scss';

interface IVerificationLabelProps {
    className?: string,
    onClick?: () => void
}

const VerificationLabel: FC<IVerificationLabelProps> = ({className, onClick}) => {
    return (
        <span
            className={classNames(s.verification, className)}
            onClick={onClick}
            title={'Вопрос находится на проверке'}
        >Проверка</span>
    );
}

export default VerificationLabel;