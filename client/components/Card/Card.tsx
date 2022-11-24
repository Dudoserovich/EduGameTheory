import React, {FC} from 'react'
import Link from '../../polyfills/link'
import s from './Card.module.scss'
import Button from '../Button'
import classNames from 'classnames'

interface IProps {
    status: string,
    href: string,
    attestableName?: string,
    specialty: string,
    grade: string,
    date: string,
    progress: string,
    attestationId?: string
}

const Card: FC<IProps> = ({
                              status,
                              href,
                              attestableName,
                              specialty,
                              grade,
                              date,
                              progress
                          }) => {
    function statusDecoration(status) {
        switch (status) {
            case 'open':
                return <b className={s.active}>открыта</b>
            case 'success':
                return <b className={s.success}>пройдена</b>
            case 'fail':
                return <b className={s.fail}>не пройдена</b>
        }
    }

    return (
        <div className={classNames(s.card, {[s.close]: status !== 'open'})}>
            <span className={s.specialty}>{specialty}</span>
            <span className={s.grade}>{grade}</span>
            {attestableName &&
                <span className={s.card_content} title={attestableName}>{`Аттестуемый: ${attestableName}`}</span>}
            <span className={s.card_content}>{`Дата создания: ${date}`}</span>
            <span className={s.card_content}>{`Прогресс: ${progress}%`}</span>
            <span className={s.card_content}>
                {`Статус: `}
                {statusDecoration(status)}
            </span>
            <Link href={href}>
                <Button className={s.button}>Подробнее</Button>
            </Link>
        </div>
    )
}

export default Card