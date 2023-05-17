import React from "react";
import Auth from "../components/Auth/Auth";
import SmallAuth from "../components/Auth/SmallAuth";
import ProjectPage from "../components/IndexPage/ProjectPage";
import TheoryPage from "../components/IndexPage/TheoryPage";
import TaskPage from "../components/IndexPage/TaskPage";
import ContactsPage from "../components/IndexPage/ContactsPage";
import AchivPage from "../components/IndexPage/AchivPage";
import Head from "../polyfills/head";
import s from '../styles/pages/index.module.scss';
import Toast, {notify} from "../components/Toast/Toast";
import auto from "chart.js/auto";
import {Hidden} from "@material-ui/core";
import logoSvg from "../public/svg/logo.svg";
import BoxAnimation from "../components/BoxAnimation/BoxAnimation";

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
            <div>
                <div className={s.background_style} id='myFullPage'>
                    <div className={s.lists}><ProjectPage/></div>
                    <div className={s.lists}><TheoryPage/></div>
                    <div className={s.lists}><AchivPage/></div>
                    <div className={s.lists}><TaskPage/></div>
                    <div className={s.lists}><ContactsPage/></div>
                    <BoxAnimation/>
                </div>
            </div>
            <div className={s.header}>
                <div className={s.headerContent}>
                    <div className={s.logo} dangerouslySetInnerHTML={{__html: logoSvg}}/>
                </div>
                <Hidden xsDown>
                <Auth/>
                    </Hidden>
                <Hidden smUp>
                    <SmallAuth/>
                </Hidden>
            </div>
        </>
    )
}