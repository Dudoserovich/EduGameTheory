import React from "react";
import {Grid, Hidden} from "@material-ui/core";
import s from './Style/ProjectPage.module.scss';
import catSvg from './cat3.svg';

export default function ProjectPage() {
    return (
        <>
            <Grid container spacing={2} className={s.about_us}>
                <Grid item sm={12} md={8} lg={6}>
                    <h1>О проекте</h1>
                    <h4>
                        Всем привет!
                    </h4>
                    <h3>
                        Данный проект создан для легкого и интересного изучения матричных методов в Теории
                        Игр.</h3>
                    <Hidden mdDown>
                        <Grid item lg={12}>
                            <h4>Мы попытались создать комфорную и понятную среду обучения, так что жмякай на кнопку и
                                скорее
                                начнем!</h4>
                        </Grid>
                        <Grid item lg={12}>
                            <button className={s.botton}>Начать</button>
                        </Grid>
                    </Hidden>
                </Grid>
                <Grid item xs={12} sm={12} md={4} lg={6}>
                    <div className={s.catSVG} dangerouslySetInnerHTML={{__html: catSvg}}/>
                </Grid>
                <Hidden lgUp>
                    <Grid item sm={12} md={12}>
                        <h4>Мы попытались создать комфорную и понятную среду обучения, так что жмякай на кнопку и
                            скорее
                            начнем!</h4>
                    </Grid>
                    <Grid item xs={12} sm={12} md={4} style={
                        {alignItems: 'center',}
                    }>
                        <button className={s.botton}>Начать</button>
                    </Grid>
                </Hidden>
            </Grid>

        </>
    )
}