import React, {useEffect} from "react";
import Auth from "../components/Auth/Auth";
import Head from "../polyfills/head";
import {MainLogo} from "../public/logos/Logos";
import {Accordion, AccordionSummary, Typography} from "@material-ui/core";
import ExpandMoreIcon from "@mui/icons-material/ExpandMore";
import {AccordionDetails, Card, CardActions, CardContent} from "@mui/material";
import {Controller} from "react-hook-form";
import Input from "../components/Input/Input";
import Spinner from "../components/Spinner/Spinner";
import Button from "../components/Button";
import s from '../styles/pages/index.module.scss';
import mySvg from '../public/logos/1.svg';
import catSvg from '../public/logos/cat3.svg';

import Toast, {notify} from "../components/Toast/Toast";

export default function Home() {
    const centerY = window.innerWidth / 9;
    const size = window.innerWidth / 2.4;

    return (
        <>
            <Head>
                <title>О нас (EduGameTheory)</title>
                <meta
                    name="viewport"
                    content="width=375, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes"
                />
            </Head>
            <div style={{
                display: 'flex',
                alignItems: 'baseline',
                justifyContent: 'space-between',
                width: '100%',
                height: '100%',
                flexDirection: 'column',
            }}>
                <div className={s.content}>
                    <div style={{
                        display: 'flex',
                        color: 'black',
                        alignItems: 'center',
                        width: '12em',
                        justifyContent: 'space-between',
                    }}>
                        <div className={s.logo}>
                            <MainLogo/>
                        </div>
                        <span className={s.name}>EduGameTheory</span>
                    </div>

                    <Auth/>
                </div>
                <div className={s.aboutUs}>
                    <div className={s.catSVG}  dangerouslySetInnerHTML={{__html: catSvg}}/>
                    <div className={s.about}>
                        <h1>О проекте</h1>
                        {/*Мы хотим немного поиграть с вами, рассказать красивую
                            историю и самое главное научить решать задачки по теории игр!</h3>*/}
                        <h4>
                            Всем привет!
                        </h4>
                        <h3>
                            Данный проект создан для легкого и интересного изучения матричных методов в Теории Игр.</h3>
                        <h4>Мы попытались создать комфорную и понятную среду обучения, так что жмякай на кнопку и скорее начнем!</h4>
                        <button  className={s.button}>Начать</button>
                    </div>
                </div>
                <div className={s.animationArea}>
                    <ul className={s.boxArea}>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                    </ul>

                </div>


            </div>
        </>
    )
}
