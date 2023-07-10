import React, {useEffect, useState} from 'react';
import s from '../../styles/tasks/tasks.module.scss';
import Page from "../../layout/Page/Page";
import {useDispatch, useSelector} from 'react-redux';
import {Grid} from "@material-ui/core";

import down from '../../public/svg/down.svg'
import up from '../../public/svg/up.svg'
import edit from '../../public/svg/edit.svg'
import deleteSVG from '../../public/svg/delete1.svg'
import closeSvg from "../../public/svg/close.svg";

import {deleteTask, getTasksInfo} from "../../store/slices/tasksSlice";
import {getUserInfo} from "../../store/slices/userSlice";
import {useNavigate} from 'react-router-dom';
import {
    Autocomplete,
    Button, Checkbox,
    Chip,
    Dialog,
    DialogContent,
    DialogTitle,
    Divider, FormControlLabel, IconButton, Tooltip
} from "@mui/material";
import TextField from "@mui/material/TextField";
import Markdown from "../../components/Markdown/Markdown";
import CustomSelect from "../../components/CustomSelect/CustomSelect";
import {getTopicsInfo} from "../../store/slices/topicSlice";
import {getTeacherUsers} from "../../store/slices/generalSlice";
import CloseIcon from '@mui/icons-material/Close';
import TopicIcon from '@mui/icons-material/Topic';
import Avatar from "@mui/material/Avatar";
import ExpandMoreIcon from '@mui/icons-material/ExpandMore';
import ExpandLessIcon from '@mui/icons-material/ExpandLess';
import EditIcon from '@mui/icons-material/Edit';
import DeleteIcon from '@mui/icons-material/Delete';
import VisibilityIcon from '@mui/icons-material/Visibility';
import AddTaskIcon from '@mui/icons-material/AddTask';
import {getTasksTeacherInfo} from "../../store/slices/teacherTasksSlice";
import {isAdmin, isNotUser} from "../../scripts/rolesConfig";
import DataArrayIcon from "@mui/icons-material/DataArray";


export default function tasks() {

    const tasks = useSelector(state => state.tasks.info);
    const user = useSelector(state => state.user.info);
    const teachers = useSelector(state => state.general.teachers);
    // const topics = useSelector(state => state.topics.info);
    const tasksSelf = useSelector(state => state.tasksTeacher.info);

    const dispatch = useDispatch();

    useEffect(() => {
        dispatch(getTasksInfo());
        // dispatch(getTopicsInfo());
        dispatch(getUserInfo());
        dispatch(getTeacherUsers());
        dispatch(getTasksTeacherInfo());
    }, []);

    const [filters, setFilters] = useState({
        tasks: [],
        topics: [],
        teachers: [],
        tasksSelf: []
    });

    const navigate = useNavigate();

    function MyTask(props) {
        const [open, setOpen] = React.useState(false);

        const handleClickOpen = () => {
            setOpen(true);
        };
        const handleClose = () => {
            setOpen(false);
        }

        const {task} = props;
        const handleClickTask = () => {
            props.navigate('/tasks/task', {
                    state:
                        {
                            task: task
                        }
                }
            );
        }

        const handleClickPlay = () => {
            props.navigate('/tasks/taskPlay', {
                    state:
                        {
                            task: task
                        }
                }
            );
        }

        return (
            <>
                <Tooltip title="Сыграть или пройти">
                    <Button
                        onClick={
                            (task.flag_matrix === 'платёжная матрица') ?
                                handleClickOpen
                                : handleClickTask}
                    >
                        Начать
                    </Button>
                </Tooltip>
                <Dialog
                    open={open}
                    onClose={handleClose}
                    aria-labelledby='form-dialog-title'
                    fullWidth={true}
                >
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
                        <div
                            style={{
                                color: "black",
                                textAlign: "center"
                            }}
                            className={s.title}
                        >
                            Выберите режим
                        </div>
                    </DialogTitle>
                    <DialogContent
                        style={{
                            alignSelf: "center"
                        }}
                        className={s.contents}
                    >
                        <Button onClick={handleClickTask}>
                            Задание
                        </Button>
                        <Button onClick={handleClickPlay}>
                            Игра
                        </Button>
                    </DialogContent>
                </Dialog>
            </>
        );
    }

    function GoEditTask(props) {
        const {task} = props;
        const handleClick = () => {
            props.navigate('/tasks/editTask', {
                    state:
                        {
                            task: task
                        }
                }
            );
        }

        return (
            <Tooltip title="Редактировать задание">
                <IconButton
                    onClick={handleClick}
                >
                    <EditIcon/>
                </IconButton>
            </Tooltip>
        );
    }

    function ListTask({task}) {
        const [showDetails, setShowDetails] = useState(false);
        return (
            <div>
                <Grid
                    container
                    spacing={0}
                    className={s.task}
                    style={{
                        display: "flex",
                        background: "white",
                        justifyContent: "space-between",
                        alignItems: "center"
                    }}
                >
                    <Grid item xs={12} sm={3} md={3} lg={3}
                          className={s.name}
                          style={{
                              display: "flex",
                              justifyContent: "center",
                              alignItems: "center"
                          }}
                    >
                        <p style={{marginRight: 10}}>{task.name}</p>
                        <Tooltip title="Подробнее">
                            <IconButton
                                // onClick={async () => {
                                //     console.log("View")
                                // }}
                                href={`/tasks/stats/${task.id}`}
                            >
                                {(task?.owner?.id === user?.data?.id) ? <VisibilityIcon/> : <></>}
                            </IconButton>
                        </Tooltip>
                    </Grid>
                    <Grid item xs={12} sm={4} md={3} lg={3} className={s.fio}>
                        {/*Создатель<br/>*/}
                        {/*{(task.owner != null) ?*/}
                        {/*    task.owner.full_name*/}
                        {/*    : "Задание кота"}*/}
                        <Chip
                            style={{color: "darkgray"}}
                            avatar={<Avatar/>}
                            label={task?.owner?.full_name ?? "Кот"}
                        />
                    </Grid>
                    <Grid item xs={12} sm={2} md={2} lg={2} className={s.topic}>
                        {/*Тип<br/>*/}
                        {/*{task.topic.name}*/}
                        <Chip
                            style={{color: "darkgray", marginBottom: 10}}
                            icon={<TopicIcon/>}
                            label={task.topic.name}
                        />
                        <Chip
                            style={{color: "darkgray", marginBottom: 10}}
                            icon={<DataArrayIcon/>}
                            label={task?.flag_matrix}/>
                    </Grid>
                    {(task.owner != null && user.data?.full_name) ?
                        (task.owner.id === user.data?.id) ?
                            <Grid container item xs={4} sm={1} md={1} lg={1}>
                                <Grid item xs={6} sm={6} md={6} lg={6}>
                                    <GoEditTask task={task} navigate={navigate}/>
                                </Grid>
                                <Grid item xs={6} sm={6} md={6} lg={6}>
                                    <Tooltip title="Удалить задание">
                                        <IconButton
                                            onClick={async () => {
                                                await dispatch(deleteTask(task.id));
                                                await dispatch(getTasksInfo());
                                            }}
                                        >
                                            <DeleteIcon/>
                                        </IconButton>
                                    </Tooltip>
                                </Grid>
                            </Grid>
                            : <Grid item xs={4} sm={1} md={1} lg={1}>
                                <div></div>
                            </Grid>
                        : <Grid item xs={4} sm={1} md={1} lg={1}>
                            <div></div>
                        </Grid>
                    }
                    <Grid container item spacing={0} xs={8} sm={2} md={2} lg={2} className={s.buttons}>
                        <Grid item xs={3} sm={3} md={3} lg={3}>
                            {showDetails ?
                                (
                                    <Tooltip title="Скрыть описание">
                                        <IconButton
                                            aria-label="more"
                                            onClick={() => setShowDetails(!showDetails)}
                                        >
                                            <ExpandLessIcon/>
                                        </IconButton>
                                    </Tooltip>
                                ) :
                                (
                                    <Tooltip title="Показать описание">
                                        <IconButton
                                            aria-label="more"
                                            onClick={() => setShowDetails(!showDetails)}
                                        >
                                            <ExpandMoreIcon/>
                                        </IconButton>
                                    </Tooltip>
                                )
                            }
                        </Grid>
                        {
                            !isNotUserSelf || isAdmin(user?.data?.roles) ?
                                <Grid item xs={9}>
                                    <MyTask task={task} navigate={navigate}/>
                                </Grid>
                                : null
                        }
                    </Grid>

                    {showDetails ?
                        (
                            <Grid item xs={12} sm={12} md={12} lg={12}>
                                <div style={{
                                    backgroundColor: 'white',
                                    width: '100%',
                                    height: '2px',
                                }}>
                                </div>
                                <div style={{background: "white", padding: 10}}>
                                    <Divider variant="middle">
                                        <Chip label="Описание"/>
                                    </Divider>
                                    <Markdown
                                        value={task?.description.trim()}
                                        style={{
                                            marginLeft: 16,
                                            marginRight: 16,
                                            textAlign: "left"
                                        }}
                                    />
                                </div>

                            </Grid>
                        )
                        :
                        (
                            <Grid item xs={12} sm={12} md={12} lg={12}>
                                <div></div>
                            </Grid>
                        )
                    }
                </Grid>
            </div>
        )
            ;
    }

    function onChangeHandler(options) {
        setFilters({...filters, ...options})
    }

    function filtering(items) {
        let result = items;
        let filteredItems = [];

        console.log(filters?.tasks)

        if (filters.topics.length !== 0) {
            for (let i = 0; i < filters.topics.length; i++) {
                filteredItems = filteredItems.concat(
                    result.filter(item =>
                        item?.topic?.id === filters.topics[i].id
                    )
                );
            }

            result = filteredItems;
        }

        if (filters.tasks.length !== 0) {
            for (let i = 0; i < filters.tasks.length; i++) {
                filteredItems = filteredItems.concat(
                    result.filter(item => item.id === filters.tasks[i].id)
                );
            }

            result = filteredItems;
        }

        if (filters.teachers.length !== 0) {
            for (let i = 0; i < filters.teachers.length; i++) {
                filteredItems = filteredItems.concat(
                    result.filter(item => item?.owner?.full_name === filters.teachers[i].full_name)
                );
            }

            result = filteredItems;
        }

        if (filters.tasksSelf.length !== 0) {
            for (let i = 0; i < filters.tasksSelf.length; i++) {
                filteredItems = filteredItems.concat(
                    result.filter(item => item.id === filters.tasksSelf[i].id)
                );
            }

            result = filteredItems;
        }

        result = new Set(result);
        result = Array.from(result);

        return result;
    }

    const [isNotUserSelf, setIsNotUserSelf] = useState(false);

    useEffect(() => {
        if (user?.data && (isNotUser(user.data.roles))) {
            setIsNotUserSelf(true)
        }
    }, [user]);

    return (
        <Page pageTitle={'Задания'}>
            <div className={s.backgroundStyle}>
                <div className={s.ctn}
                     style={{
                         textAlign: "center"
                     }}
                >
                    <div
                        style={{
                            width: "100%",
                            display: "flex",
                            justifyContent: "flex-start",
                            alignItems: "flex-start",
                            marginBottom: 10,
                            flexWrap: "wrap"
                        }}
                    >
                        <Autocomplete
                            disabled={filters?.teachers?.length > 0 || filters?.tasksSelf?.length > 0}
                            multiple
                            className={s.filter}
                            limitTags={2}
                            id="multiple-limit-tags"
                            size="small"
                            options={tasks.data ?? []}
                            getOptionLabel={option => option.name}
                            renderInput={(params) =>
                                <TextField sx={{background: "white"}} {...params}
                                           label={tasks?.data?.length ? tasks?.data?.length + " заданий" : "... заданий"}
                                           placeholder="Название"
                                />
                            }
                            onChange={(e, v) => onChangeHandler({tasks: v})}
                        />
                        <Autocomplete
                            disabled={filters?.tasks?.length > 0 || filters?.tasksSelf?.length > 0}
                            multiple
                            className={s.filter}
                            limitTags={2}
                            id="multiple-limit-tags"
                            size="small"
                            options={teachers?.data ?? []}
                            getOptionLabel={option => option.full_name}
                            renderInput={(params) =>
                                <TextField sx={{background: "white"}} {...params}
                                           label={teachers?.data?.length ? teachers?.data?.length + " преподавателей" : "... преподавателей"}
                                           placeholder="Создатель"
                                />
                            }
                            onChange={(e, v) => onChangeHandler({teachers: v})}
                        />

                        {
                            isNotUserSelf ?
                                <>
                                    <FormControlLabel
                                        disabled={filters?.teachers?.length > 0 || filters?.tasks?.length > 0}
                                        control={
                                            <Checkbox
                                                onChange={(e, v) => {
                                                    if (v)
                                                        onChangeHandler({tasksSelf: tasksSelf?.data ?? []})
                                                    else onChangeHandler({tasksSelf: []})
                                                }}
                                            />
                                        }
                                        label="Мои задания"
                                    />

                                    <Tooltip title="Создать задание">
                                        <IconButton
                                            aria-label="more"
                                            label="gg"
                                            href="/tasks/createTask"
                                        >
                                            <AddTaskIcon/>
                                        </IconButton>
                                    </Tooltip>
                                </>
                                : null
                        }

                        {/*<CustomSelect*/}
                        {/*    isMulti*/}
                        {/*    instanceId={'topic-select'}*/}
                        {/*    className={s.filter}*/}
                        {/*    placeholder={'Тип'}*/}
                        {/*    closeMenuOnSelect={false}*/}
                        {/*    isClearable={true}*/}
                        {/*    isSearchable={false}*/}
                        {/*    isLoading={topics.isLoading}*/}
                        {/*    loadingMessage={() => 'Загрузка...'}*/}
                        {/*    noOptionsMessage={() => {*/}
                        {/*        return (topics.error ? 'Ошибка сервера' : 'Ничего нет :(');*/}
                        {/*    }}*/}
                        {/*    options={topics.data}*/}
                        {/*    getOptionLabel={option => option.name}*/}
                        {/*    getOptionValue={option => option.id}*/}
                        {/*    onChange={options => onChangeHandler({topics: options})}*/}
                        {/*/>*/}
                    </div>
                    <div>
                        {
                            tasks?.data ?
                                filtering(tasks?.data)?.map((task) => (
                                        <ListTask key={task.id} task={task}/>
                                    )
                                )
                                : "Loading..."
                        }
                    </div>
                </div>

            </div>
        </Page>
    );
}