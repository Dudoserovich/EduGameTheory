import React from 'react';
import s from '../styles/pages/404.module.scss';
import router from '../polyfills/router';
import Button from '../components/Button';

export default function NotFound() {
    return (
        <div className={s.ctn}>
            <span className={s.numbers}>404</span>
            <span className={s.title}>Страница не найдена</span>
            <Button onClick={() => router.back()} className={s.button}>Вернуться назад</Button>
        </div>
    );
}