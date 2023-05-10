import React, {useEffect} from "react";
import Auth from "../components/Auth/Auth";
import ProjectPage from "../components/IndexPage/ProjectPage";
import TheoryPage from "../components/IndexPage/TheoryPage";
import Head from "../polyfills/head";
import {MainLogo} from "../public/logos/Logos";
import {Grid, Hidden} from "@material-ui/core";
import {Controller} from "react-hook-form";
import s from '../styles/pages/index.module.scss';

import Toast, {notify} from "../components/Toast/Toast";
import auto from "chart.js/auto";

export default function Home() {
    return (
        <>
            <Head>
                <title>О проекте (EduGameTheory)</title>
                <meta
                    name="viewport"
                    content="width=375, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes"
                />
            </Head>
            <div className={s.background_style}>
                <div className={s.cont}>
                <div className={s.lists}><ProjectPage/></div>
                <div className={s.lists}> <TheoryPage/></div>
            </div>
                <ul className={s.boxArea}>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                </ul>
            </div>
            <div className={s.content}>
            <div style={{
                display: 'flex',
                alignItems: 'center',
                marginLeft: '20px',
                marginRight: '20px',
            }}>
                <MainLogo className={s.logo}/>
                <span className={s.name}>EduGameTheory</span>
            </div>
            <Auth/>
        </div>
        </>
    )
}