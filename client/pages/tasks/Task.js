import React, {useEffect, useState} from 'react';
import s from '../../styles/tasks/task.module.scss';
import Page from "../../layout/Page/Page";
import {useDispatch, useSelector} from 'react-redux';
import {Grid} from "@material-ui/core";
import {useLocation} from 'react-router-dom';
import {getStrategyInfo} from "../../store/slices/typeTaskSlice";
import {Controller, useForm} from "react-hook-form";
import TextField from "@mui/material/TextField";
import MenuItem from "@mui/material/MenuItem";
import check from "../../public/svg/check.svg";
import {Button, Dialog, DialogContent, DialogTitle, IconButton} from "@mui/material";
import {TaskPayoff} from "../../store/slices/taskPayoffSlice";
import {TaskGame} from "../../store/slices/taskGameSlice";
import Markdown from "../../components/Markdown/Markdown";
import CloseIcon from "@mui/icons-material/Close";


export default function tasks() {
    const {state} = useLocation();
    const task = state.task;
    const taskGame = useSelector(state => state.taskGame.info);
    const taskPayoff = useSelector(state => state.taskPayoff.info);
    const strategy = useSelector(state => state.strategy.info);
    const dispatch = useDispatch();
    console.log(taskPayoff)

    useEffect(() => {
        dispatch(getStrategyInfo());
    }, []);

    const [strate, setStrategyies] = useState("");
    console.log(task)

    function Matrix() {
        return (
            <table className={s.backgroundMatrix}>
                <tbody>
                <tr>
                    {
                        (task.name_first_player !== null && task.name_second_player !== null) ?


                            (<td>{task.name_first_player} /<br/>{task.name_second_player}</td>)
                            : (<td>1-ый игрок /<br/>2-ой игрок</td>)
                    }
                    {
                        (task.name_first_strategies !== null) ?
                            task.name_first_strategies.map((strategies, index) => (

                                (<td key={index} className={s.col}>{strategies}</td>)
                            ))
                            :
                            task.matrix[0].map((strategies, index) => (
                                <td key={index} className={s.col}>{index + 1}-ая стратегия</td>

                            ))
                    }
                </tr>
                {task.matrix.map((row, rowIndex) => (
                    <tr key={rowIndex}>
                        {/* Дополнительная ячейка слева с подписью для каждой строки */}
                        {
                            (task.name_second_strategies !== null) ?
                                (<td key={rowIndex} className={s.col}>{task.name_second_strategies[rowIndex]}</td>)
                                :
                                <td key={rowIndex} className={s.col}>{rowIndex + 1}-ая стратегия</td>
                        }
                        {row.map((cell, cellIndex) => (
                            <td key={cellIndex} className={s.col}>{cell}</td>
                        ))}
                    </tr>
                ))}
                </tbody>
            </table>
        );
    }

    function GamePayoff() {
        const {handleSubmit, control, formState: {errors}} = useForm({
            mode: 'onBlur',
            defaultValues: {
                strategy: strate,
                first_player: [0],
                second_player: [0],
                game_price: 0,
            }
        });

        return (<Grid item xs={12} sm={12} md={12} lg={12} className={s.name} >
            <form onSubmit={
                (strate === "смешанные стратегии") ?
                    handleSubmit(onSubmit) :
                    handleSubmit(onSubmitClear)
            }>
                <Grid container spacing={0}>
                    <Grid item xs={12} sm={6} md={4} lg={2} className={s.name} >
                        <Controller
                            name="strategy"
                            control={control}
                            rules={{required: true}}
                            render={({field}) => (
                                <TextField
                                    {...field}
                                    required
                                    type={"text"}
                                    id="outlined-select-currency"
                                    select
                                    label="Тип марицы"
                                    defaultValue={strate}
                                    style={{
                                        minWidth: '200px',
                                        background: "white"
                                    }}
                                >
                                    {
                                        strategy?.data ?
                                            strategy?.data.map((strates) => (
                                                    <MenuItem key={strates} value={strates}
                                                              onClick={() => setStrategyies(strates)}>
                                                        {strates}
                                                    </MenuItem>
                                                )
                                            )

                                            : "Loading..."
                                    }
                                </TextField>
                            )}/>
                        {errors.strategy &&
                            <span style={{
                                color: 'var(--main-brand-color)',
                                fontSize: "large"
                            }}>Обязательное поле</span>}
                    </Grid>
                    <Grid item xs={12} sm={6} md={4} lg={3}
                          className={s.name}
                    style={{padding: '0 10px'}}>
                        <Controller
                            name="first_player"
                            control={control}
                            rules={{required: true}}
                            render={({field}) => (
                                <TextField
                                    {...field}
                                    required
                                    type={"number[]"}
                                    color="info"
                                    style={{
                                        borderRadius: '4px',
                                        backgroundColor: "white",
                                        width: '100%',
                                    }}
                                    id="outlined-helperText"
                                    label="Стратегия первого игрока"
                                    defaultValue=""
                                />
                            )}/>
                        {errors.first_player &&
                            <span style={{
                                color: 'var(--main-brand-color)',
                                fontSize: "large"
                            }}>Обязательное поле</span>}
                    </Grid>
                    <Grid item xs={12} sm={6} md={4} lg={3}
                          className={s.name}>
                        <Controller
                            name="second_player"
                            control={control}
                            rules={{required: true}}
                            render={({field}) => (
                                <TextField
                                    {...field}
                                    required
                                    type={"number[]"}
                                    color="info"
                                    style={{
                                        borderRadius: '4px',
                                        backgroundColor: "white",
                                        width: '100%',
                                    }}
                                    id="outlined-helperText"
                                    label="Стратегия второго игрока"
                                    defaultValue=""
                                />
                            )}/>
                        {errors.second_player &&
                            <span style={{
                                color: 'var(--main-brand-color)',
                                fontSize: "large"
                            }}>Обязательное поле</span>}
                    </Grid>
                    <Grid item xs={12} sm={6} md={4} lg={3} className={s.name}
                          style={{padding: '0 10px'}}>
                        <Controller
                            name="game_price"
                            control={control}
                            rules={{required: true}}
                            render={({field}) => (
                                <TextField
                                    {...field}
                                    required
                                    type={"number"}
                                    color="info"
                                    style={{
                                        borderRadius: '4px',
                                        backgroundColor: "white",
                                        width: '100%',
                                    }}
                                    id="outlined-helperText"
                                    label="Цена игры"
                                />
                            )}/>
                        {errors.game_price &&
                            <span style={{
                                color: 'var(--main-brand-color)',
                                fontSize: "large"
                            }}>Обязательное поле</span>}
                    </Grid>

                </Grid>
                {
                    <Button type={'submit'} variant="contained">Проверить решение</Button>
                }
            </form>
        </Grid>);
    }

    const [open, setOpen] = React.useState(false);

    function Game() {

        const {handleSubmit, control, formState: {errors}} = useForm({
            mode: 'onBlur',
            defaultValues: {
                min_value: 0,
                min_index: 0,
            }
        });

        return (<Grid item xs={12} sm={12} md={12} lg={12} className={s.name}>
            <form onSubmit={handleSubmit(onSubmitPos)}>
                <Grid container spacing={0}>
                    <Grid item xs={12} sm={12} md={12} lg={12} className={s.name}>
                        <Controller
                            name="min_value"
                            control={control}
                            rules={{required: true}}
                            render={({field}) => (
                                <TextField
                                    {...field}
                                    required
                                    type={"number[]"}
                                    color="info"
                                    style={{
                                        borderRadius: '4px',
                                        backgroundColor: "white",
                                        width: '100%',
                                    }}
                                    id="outlined-helperText"
                                    label="Минимальное значение"
                                    defaultValue=""
                                />
                            )}/>
                        {errors.min_value &&
                            <span style={{
                                color: 'var(--main-brand-color)'
                            }}>Обязательное поле</span>}
                    </Grid>
                    <Grid item xs={12} sm={12} md={12} lg={12}
                          className={s.name}>
                        <Controller
                            name="min_index"
                            control={control}
                            rules={{required: true}}
                            render={({field}) => (
                                <TextField
                                    {...field}
                                    required
                                    type={"number[]"}
                                    color="info"
                                    style={{
                                        borderRadius: '4px',
                                        backgroundColor: "white",
                                        width: '100%',
                                    }}
                                    id="outlined-helperText"
                                    label="Минимальный индекс"
                                    defaultValue=""
                                />
                            )}/>
                        {errors.min_index &&
                            <span style={{
                                color: 'var(--main-brand-color)'
                            }}>Обязательное поле</span>}
                    </Grid>
                </Grid>
                {<>
                    <Button type={'submit'}>Проверить решение</Button>
                </>
                }
            </form>
        </Grid>);
    }

    const handleClickOpen = () => {
        setOpen(true);
    };
    const handleClose = () => {
        setOpen(false);
    }
    const onSubmitClear = (data) => {
        const TasksPayoffClear = {
            strategy: strate,
            first_player: parseFloat(data.first_player),
            second_player: parseFloat(data.second_player),
            game_price: parseFloat(data.game_price),
        }
        handleClickOpen();
        dispatch(TaskPayoff({id: task.id, ITaskPayoff: TasksPayoffClear}));
    }
    const onSubmitPos = (data) => {
        const TasksPayoffClear = {
            min_value: parseFloat(data.min_value),
            min_index: parseFloat(data.min_index),
        }
        handleClickOpen();
        dispatch(TaskGame({id: task.id, ITaskPayoff: TasksPayoffClear}));
    }
    const onSubmit = (data) => {
        const TasksPayoffClear = {
            strategy: strate,
            first_player: JSON.parse(data.first_player),
            second_player: JSON.parse(data.second_player),
            game_price: parseFloat(data.game_price),
        }
        handleClickOpen();
        dispatch(TaskPayoff({id: task.id, ITaskPayoff: TasksPayoffClear}));
    }
    return (
        <Page pageTitle={'Задания'}>
            <div className={s.backgroundStyle}>
                <div className={s.ctn}>
                    <Grid container spacing={0} className={s.background}>
                        <Grid item xs={12} sm={12} md={12} lg={12} className={s.name}>
                            {task.name}
                        </Grid>
                        <Grid container item xs={6} sm={6} md={6} lg={6} className={s.propsText}>
                            <Grid item xs={2} sm={1} md={1} lg={1}>
                                <div className={s.check}
                                     dangerouslySetInnerHTML={{__html: check}}/>
                            </Grid>
                            <Grid item xs={9} sm={10} md={10} lg={10}>
                                {task.topic.name}
                            </Grid>
                        </Grid>
                        <Grid container item xs={6} sm={6} md={6} lg={6} className={s.propsText}>
                            <Grid item xs={2} sm={1} md={1} lg={1}>
                                <div className={s.check}
                                     dangerouslySetInnerHTML={{__html: check}}/>
                            </Grid>
                            <Grid item xs={9} sm={10} md={10} lg={10}>
                                {task.flag_matrix}
                            </Grid>
                        </Grid>
                        <Grid item xs={12} sm={12} md={12} lg={12} className={s.title}>
                            Описание
                        </Grid>
                        <Grid item xs={12} sm={12} md={12} lg={12}
                        >
                            <Markdown
                                className={s.descriptionsR}
                                value={task?.description.trim()}
                            />
                        </Grid>
                        <Grid item xs={12} sm={12} md={12} lg={12} className={s.title}>
                            Подсказки
                        </Grid>
                        <Grid item xs={12} sm={12} md={12} lg={12} className={s.descriptionsR}>
                            Чтобы решить это задние вы должны найти оптимальные стратегии для каждого игрока.<br/>
                            Для этого вам нужно определить является ли игра чистой стратегией или смешанной.
                        </Grid>
                        <Grid item xs={12} sm={10} md={10} lg={10} className={s.descriptionsR}>
                            Помните что строки матрицы - это стратегии 1-го игрока, а столбцы - это стратегии
                            второго
                            игрока.
                        </Grid>
                        <Grid item xs={12} sm={12} md={12} lg={12} className={s.title}>
                            Матрица
                        </Grid>
                        <Grid container item xs={12} sm={12} md={12} lg={12} style={{overflowX: 'auto', width: '100%', justifyContent: 'center', }}
                        >
                                <div style={{maxWidth: "fit-content", overflowX: 'auto', }}>
                                    <Matrix matrix={task?.matrix}/></div>
                        </Grid>
                        <Grid container item xs={12} sm={12} md={12} lg={12} className={s.title}>
                            Pешение
                            {
                                (task.flag_matrix === "платёжная матрица") ?
                                    (<GamePayoff/>)
                                    :
                                    (<Game/>)
                            }
                        </Grid>

                    </Grid>
                    <Dialog open={open} onClose={handleClose} aria-labelledby='form-dialog-title' fullWidth={true}>
                        <DialogTitle id='form-dialog-title' className={s.back}>
                            <IconButton
                                aria-label="close"
                                onClick={handleClose}
                                sx={{
                                    position: 'absolute',
                                    right: 8,
                                    top: 8,
                                    color: (theme) => theme.palette.grey[500],
                                }}
                            >
                                <CloseIcon/>
                            </IconButton>
                            {taskPayoff?.data?.success ?
                                (taskPayoff.data.success === true) ?
                                    (<div style={{color: 'green'}}>Успех</div>)
                                    :
                                    (<div style={{color: 'red'}}>Провал</div>)
                                :
                                (<div></div>)
                            }
                        </DialogTitle>
                        <DialogContent style={{color: 'black'}}>
                            {taskGame?.data ?
                                (<div>{taskGame.data}</div>)
                                : taskPayoff?.data?.message ?
                                    (<div>{taskPayoff.data.message}</div>)
                                    : (<div>Загрузка...</div>)

                            }
                            <Button onClick={handleClose}>
                                Закрыть
                            </Button>
                        </DialogContent>
                    </Dialog>
                </div>
            </div>
        </Page>
    );
}