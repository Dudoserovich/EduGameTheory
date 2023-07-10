import React, {useEffect, useState} from 'react';
import Page from '../../layout/Page/Page';
import {useDispatch, useSelector} from 'react-redux';
import s from "../../styles/pages/profile.module.scss";
import {useParams} from "react-router-dom";
import {educationAdd, getEducation, getEducationBlocks} from "../../store/slices/educationSlice";


import PropTypes from 'prop-types';
import Tabs from '@mui/material/Tabs';
import Tab from '@mui/material/Tab';
import Box from "@mui/material/Box";
import {Grid, Typography} from "@material-ui/core";
import Markdown from "../../components/Markdown/Markdown";
import {
    BottomNavigation,
    BottomNavigationAction,
    Button,
    Chip,
    Dialog, DialogContent,
    DialogTitle, IconButton,
    Rating
} from "@mui/material";
import Paper from "@mui/material/Paper";

import TextSnippetIcon from '@mui/icons-material/TextSnippet';
import TaskIcon from '@mui/icons-material/Task';
import CheckCircleIcon from "@mui/icons-material/CheckCircle";
import CheckCircleOutlineIcon from "@mui/icons-material/CheckCircleOutline";
import router from '../../polyfills/router';
import CloseIcon from "@mui/icons-material/Close";
import {Controller, useForm} from "react-hook-form";
import TextField from "@mui/material/TextField";
import MenuItem from "@mui/material/MenuItem";
import {TaskPayoff} from "../../store/slices/taskPayoffSlice";
import {TaskGame} from "../../store/slices/taskGameSlice";
import {getStrategyInfo} from "../../store/slices/typeTaskSlice";

function TabPanel(props) {
    const {children, value, index, ...other} = props;

    return (
        <div
            role="tabpanel"
            hidden={value !== index}
            id={`vertical-tabpanel-${index}`}
            aria-labelledby={`vertical-tab-${index}`}
            {...other}
        >
            {value === index && (
                <Box sx={{p: 3}}>
                    <Typography>{children}</Typography>
                </Box>
            )}
        </div>
    );
}

TabPanel.propTypes = {
    children: PropTypes.node,
    index: PropTypes.number.isRequired,
    value: PropTypes.number.isRequired,
};

function a11yProps(index) {
    return {
        id: `vertical-tab-${index}`,
        'aria-controls': `vertical-tabpanel-${index}`,
    };
}


export default function education() {
    const params = useParams();
    const {eduId} = params;
    const dispatch = useDispatch();
    const education = useSelector(state => state.education.edu)
    const blocks = useSelector(state => state.education.blocks)
    const taskGame = useSelector(state => state.taskGame.info);
    const taskPayoff = useSelector(state => state.taskPayoff.info);
    const strategy = useSelector(state => state.strategy.info);
    useEffect(() => {
        dispatch(getStrategyInfo());
    }, []);

    const [strate, setStrategyies] = useState("");

    // Получение объекта достижения и его блоков
    useEffect(() => {
        eduId
        && dispatch(getEducation(eduId))
        && dispatch(getEducationBlocks(eduId))
    }, [eduId]);

    // Если обучение не найдено, выкинуть 404
    useEffect(() => {
        if (education?.error?.status) {
            router.push('/404');
        }
    }, [education]);

    // console.log(education)
    // console.log(blocks)

    const [blockNumber, setBlockNumber] = React.useState(0);
    const [part, setPart] = React.useState(0);
    const [start, setStart] = React.useState(false);

    const handleChangeBlock = (event, newBlockNumber) => {
        setPart(0);
        setBlockNumber(newBlockNumber);
    };
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

        return (<Grid item xs={12} sm={12} md={12} lg={12}>
            <form onSubmit={
                (strate === "смешанные стратегии") ?
                    handleSubmit(onSubmit) :
                    handleSubmit(onSubmitClear)
            }>
                <Grid container spacing={0}>
                    <Grid item xs={12} sm={12} md={6} lg={6} style={{marginBottom: '10px'}} >
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
                    <Grid item xs={12} sm={12} md={6} lg={6}
                          style={{paddingBottom: '10px '}}>
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
                    <Grid item xs={12} sm={12} md={5} lg={5}
                          style={{marginBottom: '10px'}}>
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
                    <Grid item xs={12} sm={12} md={6} lg={6}
                          style={{marginBottom: '10px'}}>
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

        return (<Grid item xs={12} sm={12} md={12} lg={12} >
            <form onSubmit={handleSubmit(onSubmitPos)}>
                <Grid container spacing={0}>
                    <Grid item xs={12} sm={12} md={12} lg={12} >
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
                    <Grid item xs={12} sm={12} md={12} lg={12}>
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
        dispatch(TaskPayoff({id: blocks.data[0].education_tasks.task.id, ITaskPayoff: TasksPayoffClear}));
        if (taskPayoff.data.success === true){
            if (blocks?.data[1]?.id){
            dispatch(educationAdd({id: education.id, idBloc: 2}))
        }  else {
                dispatch(educationAdd({id: education.id, idBloc: 1}))
            }
        }
    }
    const onSubmitPos = (data) => {
        const TasksPayoffClear = {
            min_value: parseFloat(data.min_value),
            min_index: parseFloat(data.min_index),
        }
        handleClickOpen();
        dispatch(TaskGame({id: blocks.data[0].education_tasks.task.id, ITaskPayoff: TasksPayoffClear}));
        if (taskGame.data.success === true){
            if (blocks?.data[1]?.id){
                dispatch(educationAdd({id: education.id, idBloc: 2}))
            }  else {
                dispatch(educationAdd({id: education.id, idBloc: 1}))
            }
        }
    }
    const onSubmit = (data) => {
        const TasksPayoffClear = {
            strategy: strate,
            first_player: JSON.parse(data.first_player),
            second_player: JSON.parse(data.second_player),
            game_price: parseFloat(data.game_price),
        }
        handleClickOpen();
        dispatch(TaskPayoff({id: blocks.data[0].education_tasks.task.id, ITaskPayoff: TasksPayoffClear}));
        if (taskPayoff.data.success === true){
            if (blocks?.data[1]?.id){
                dispatch(educationAdd({id: education.id, idBloc: 2}))
            }  else {
                dispatch(educationAdd({id: education.id, idBloc: 1}))
            }
        }
    }
    return (
        <Page pageTitle={'Обучение'}>
            <div className={s.backgroundStyle}>
                <div className={s.ctn}>
                    {!start ?
                        // Информация об обучении
                        <Box
                            sx={{
                                bgcolor: 'background.paper',
                            }}
                            p={5}
                        >
                            <Typography variant="h6" gutterBottom>
                                {education?.data?.name}
                            </Typography>
                            <Markdown
                                value={education?.data?.description.trim()}
                            />

                            <Chip
                                key={education?.data?.topic?.id}
                                label={education?.data?.topic?.name}
                                style={{marginTop: "10px"}}
                            />
                            <div style={{marginTop: 10, color: "dimgray"}}>
                                <Typography component="legend">Прогресс</Typography>
                                <Rating
                                    name="disabled"
                                    value={education?.data?.progress?.passed}
                                    max={education?.data?.progress?.total}
                                    icon={<CheckCircleIcon/>}
                                    emptyIcon={<CheckCircleOutlineIcon/>}
                                    disabled
                                />
                            </div>

                            <Button
                                variant="contained"
                                onClick={() => {
                                    setStart(true);
                                    if (blocks?.data[1]?.id){
                                        dispatch(educationAdd({id: education.data.id, idBloc: 1}))
                                    } else {
                                    }
                                }}
                            >
                                Начать обучение
                            </Button>
                        </Box>
                        :
                        // Обучающие блоки
                        <Box
                            sx={{
                                flexGrow: 1,
                                bgcolor: 'background.paper',
                                display: 'flex'
                            }}
                        >
                            <Tabs
                                orientation="vertical"
                                variant="scrollable"
                                value={blockNumber}
                                onChange={handleChangeBlock}
                                aria-label="Vertical tabs example"
                                sx={{
                                    borderRight: 1,
                                    borderColor: 'divider',
                                    minWidth: 'fit-content'
                                }}
                            >
                                {
                                    blocks?.data ?
                                        blocks?.data.map(block => {
                                            return (
                                                <Tab
                                                    icon={block?.success ? <CheckCircleIcon/> :
                                                        <CheckCircleOutlineIcon/>}
                                                    iconPosition="end"
                                                    label={"Блок " + block?.education_tasks?.block_number}
                                                    {...a11yProps(block?.education_tasks?.block_number)}
                                                />
                                            )
                                        })
                                        : "Loading..."
                                }
                            </Tabs>
                            {
                                blocks?.data ?
                                    blocks?.data.map(block => {
                                        return (
                                            <TabPanel
                                                value={blockNumber}
                                                index={block?.education_tasks?.block_number - 1}
                                            >
                                                {
                                                    part === 0 ?
                                                        (
                                                            <Markdown
                                                                value={
                                                                    block?.education_tasks?.theory_text.trim()
                                                                }
                                                            />
                                                        ) : (
                                                            <div>
                                                                <Markdown
                                                                    value={
                                                                        block?.education_tasks?.task?.description.trim()
                                                                    }
                                                                />
                                                                <Grid container item xs={12} sm={12} md={12} lg={12}
                                                                      style={{margin: '10px 0'}}>
                                                                    Pешение
                                                                    {
                                                                        (blocks.data[0].education_tasks.task.flag_matrix === "платёжная матрица") ?
                                                                            (<GamePayoff/>)
                                                                            :
                                                                            (<Game/>)
                                                                    }
                                                                </Grid>
                                                                <Dialog open={open} onClose={handleClose}
                                                                        aria-labelledby='form-dialog-title'
                                                                        fullWidth={true}>
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
                                                        )
                                                }

                                                {/*Кнопки навигации Теории и Практики*/}
                                                <Paper elevation={2} sx={{width: 'fit-content', marginTop: 2}}>
                                                    <BottomNavigation
                                                        showLabels
                                                        value={part}
                                                        onChange={(event, newValue) => {
                                                            setPart(newValue);
                                                        }}
                                                    >
                                                        <BottomNavigationAction label="Теория"
                                                                                icon={<TextSnippetIcon/>}/>
                                                        <BottomNavigationAction label="Практика" icon={<TaskIcon/>}/>
                                                    </BottomNavigation>
                                                </Paper>
                                            </TabPanel>
                                        )
                                    })
                                    : "Loading..."
                            }
                        </Box>
                    }
                </div>
            </div>
        </Page>
    );
}