import React, {useEffect, useState} from 'react';
import Page from '../../../layout/Page/Page';
import {useDispatch, useSelector} from 'react-redux';
import s from "../../../styles/pages/profile.module.scss";
import {useParams} from "react-router-dom";

import Box from "@mui/material/Box";
import {Grid, Typography} from "@material-ui/core";
import Markdown from "../../../components/Markdown/Markdown";
import {
    Chip,
    Divider,
    IconButton,
    List,
    ListItem,
    ListItemAvatar,
    ListItemText,
    Rating, Tooltip
} from "@mui/material";

import CheckCircleIcon from "@mui/icons-material/CheckCircle";
import CheckCircleOutlineIcon from "@mui/icons-material/CheckCircleOutline";
import router from '../../../polyfills/router';
import Avatar from "@mui/material/Avatar";
import ExpandMoreIcon from "@mui/icons-material/ExpandMore";
import ExpandLessIcon from "@mui/icons-material/ExpandLess";
import Matrix from "../../../components/Matrix/Matrix";
import TopicIcon from "@mui/icons-material/Topic";
import DataArrayIcon from '@mui/icons-material/DataArray';
import InsightsIcon from '@mui/icons-material/Insights';
import {getTaskStats} from "../../../store/slices/taskStats";
import TextField from "@mui/material/TextField";


export default function statsTask() {
    const params = useParams();
    const {taskId} = params;
    const dispatch = useDispatch();
    const [showDetails, setShowDetails] = useState(false);
    const taskStats = useSelector(state => state.taskStatsSlice.info);

    // Получение объекта достижения и его блоков
    useEffect(() => {
        taskId && dispatch(getTaskStats(taskId))
    }, [taskId]);

    console.log(taskStats)

    // Если задание не найдено, выкинуть 404
    useEffect(() => {
        if (taskStats?.error?.status) {
            router.push('/404');
        }
    }, [taskStats]);

    return (<Page pageTitle={'Статистика по заданию'}>
            <div className={s.backgroundStyle}>
                <div className={s.ctn}>
                    <Box
                        sx={{
                            bgcolor: 'background.paper',
                        }}
                        p={5}
                    >
                        <div
                            style={{
                                display: "flex", alignItems: "baseline", justifyContent: "center", flexWrap: "wrap"
                            }}
                        >
                            <Typography
                                variant="h4"
                                style={{
                                    color: "black", marginRight: 10
                                }}
                                gutterBottom
                            >
                                {taskStats?.data?.task?.name}
                            </Typography>
                            <div
                                style={{
                                    display: "flex", justifyContent: "center", flexWrap: "wrap"
                                }}
                            >
                                <Chip
                                    style={{color: "darkgray", marginRight: 10}}
                                    icon={<TopicIcon/>}
                                    label={taskStats?.data?.task?.topic?.name}/>
                                <Chip
                                    style={{color: "darkgray"}}
                                    icon={<DataArrayIcon/>}
                                    label={taskStats?.data?.task?.flag_matrix}/>
                            </div>
                        </div>
                        <div
                            style={{
                                display: "flex", justifyContent: "center", flexDirection: "column", alignItems: "center"
                            }}
                        >
                            <Typography variant="h6">Матрица</Typography>
                            {taskStats.isLoading ? <div>Загрузка матрицы...</div> :
                                <Matrix matrix={taskStats?.data?.task?.matrix}/>}
                        </div>
                        <Grid item xs={12} sm={12} md={12} lg={12}
                        >
                            <Divider
                                variant="middle"
                                // textAlign="right"
                            >
                                <div
                                    style={{
                                        display: "flex", alignItems: "center"
                                    }}
                                >
                                    <Typography variant="h6">Описание</Typography>
                                    {showDetails ? (<IconButton
                                        aria-label="more"
                                        onClick={() => setShowDetails(!showDetails)}
                                    >
                                        <Tooltip title="Скрыть описание">
                                            <ExpandLessIcon/>
                                        </Tooltip>
                                    </IconButton>) : (<IconButton
                                        aria-label="more"
                                        onClick={() => setShowDetails(!showDetails)}
                                    >
                                        <Tooltip title="Показать описание">
                                            <ExpandMoreIcon/>
                                        </Tooltip>
                                    </IconButton>)}
                                </div>
                            </Divider>
                        </Grid>
                        {showDetails ? <Grid item xs={12} sm={12} md={12} lg={12}>
                            <div style={{
                                backgroundColor: 'white', width: '100%', height: '2px',
                            }}>
                            </div>
                            <div
                                style={{
                                    background: "white", margin: "0px 16px 16px 16px"
                                }}
                            >
                                <Markdown
                                    value={taskStats?.data?.task?.description}
                                />
                            </div>
                            <Divider
                                variant="middle"
                                // textAlign="right"
                            />
                        </Grid> : <div></div>}

                        <List
                            sx={{
                                width: '100%', bgcolor: 'background.paper'
                            }}
                            subheader={
                                <div style={{
                                    display: "flex",
                                    alignItems: "center"
                                }}
                                >
                                    <Typography
                                        variant="h6"
                                    >
                                        Статистика по пользователям
                                    </Typography>
                                    <InsightsIcon/>
                                </div>}
                        >
                            <div
                                style={{
                                    display: "grid",
                                    gridTemplateColumns: "repeat(auto-fill, minmax(250px, 1fr))",
                                    rowGap: 16
                                }}
                            >
                                {taskStats.isLoading ?
                                    <div>Статистика загружается...</div>
                                    : taskStats?.data?.users.map((user) => {
                                        const date = new Date(user.task_result.updated_at);

                                        const formattedDate = date.getFullYear() + '-' + (date.getMonth() + 1).toString().padStart(2, '0') + '-' + date.getDate().toString().padStart(2, '0') + 'T' + date.getHours().toString().padStart(2, '0') + ':' + date.getMinutes().toString().padStart(2, '0');

                                        return (<ListItem
                                            style={{display: "block"}}
                                        >
                                            <ListItemAvatar>
                                                <Avatar src={user.avatar_base64}/>
                                            </ListItemAvatar>
                                            <ListItemText
                                                primary={user.fio}
                                                secondary={<div
                                                    style={{
                                                        display: "flex", alignItems: "center"
                                                    }}
                                                >
                                                    <TextField
                                                        disabled
                                                        id="datetime-local"
                                                        label="Дата прохождения"
                                                        variant="standard"
                                                        type="datetime-local"
                                                        defaultValue={formattedDate}
                                                        InputLabelProps={{
                                                            shrink: true,
                                                        }}
                                                    />
                                                </div>}
                                            />
                                            <div>
                                                <Chip
                                                    label={"Кол-во попыток: " + user.task_result.count_tries}/>
                                            </div>
                                            <div
                                                style={{
                                                    marginTop: 10,
                                                    color: "dimgray"
                                                }}
                                            >
                                                <Typography component="legend">
                                                    Оценка
                                                </Typography>
                                                <Rating
                                                    name="disabled"
                                                    value={user.task_result.rating}
                                                    max={5}
                                                    icon={<CheckCircleIcon/>}
                                                    emptyIcon={<CheckCircleOutlineIcon/>}
                                                    disabled
                                                />
                                            </div>
                                        </ListItem>)
                                    })}
                                {!taskStats.isLoading && taskStats?.data?.users ?
                                    <div>Здесь пока пусто :(</div>
                                    : null
                                }
                            </div>
                        </List>
                    </Box>
                </div>
            </div>
        </Page>
    )
        ;
}