import React, {useEffect, useState} from 'react';
import s from '../../styles/tasks/tasks.module.scss';
import Page from "../../layout/Page/Page";
import BoxAnimation from "../../components/BoxAnimation/BoxAnimation";
import {useDispatch, useSelector} from 'react-redux';
import {Grid} from "@material-ui/core";
import down from '../../public/svg/down.svg'
import up from '../../public/svg/up.svg'
import edit from '../../public/svg/edit.svg'
import delet from '../../public/svg/delete1.svg'
import {getTasksInfo} from "../../store/slices/tasksSlice";
import {getUserInfo} from "../../store/slices/userSlice";
import {useLocation, useNavigate} from 'react-router-dom';
import {Controller} from "react-hook-form";
import TextField from "@mui/material/TextField";
import MenuItem from "@mui/material/MenuItem";


export default function tasks() {
    const { state } = useLocation();
    const task = state.task;

    return (
        <Page pageTitle={'Задания'}>
            <div className={s.backgroundStyle}>
                <div className={s.ctn}>
                    <Grid container spacing={0} className={s.background}>
                        <Grid item xs={6} sm={6} md={6} lg={6} className={s.name}>
                            {task.topic.name}
                        </Grid>
                        <Grid item xs={6} sm={6} md={6} lg={6} className={s.name}>
                            {task.flag_matrix}
                        </Grid>
                        <Grid item xs={12} sm={12} md={12} lg={12} className={s.name}>
                            {task.name}
                        </Grid>
                        <Grid item xs={12} sm={12} md={12} lg={12} className={s.name}>
                            {task.description}
                        </Grid>
                    </Grid>

                </div>
                <ul className={s.boxArea}>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                </ul>
                <BoxAnimation/>

            </div>
        </Page>
    );
}