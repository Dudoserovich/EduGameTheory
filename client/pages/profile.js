import React, {useEffect, useState} from 'react';
import s from '../styles/pages/profile.module.scss';
import Page from "../layout/Page/Page";
import {Controller, useForm} from "react-hook-form";
import {getUserInfo, updateUserInfo} from '../store/slices/userSlice';
import {useDispatch, useSelector} from 'react-redux';
import starSVG from "../public/svg/star.svg";
import levelSVG from "../public/svg/level.svg";
import {
    ProfileHeaderSkeleton,
} from '../components/Skeletons/ProfileSkeleton';
import ProfileInput from "../components/Input/ProfileInput";
import catSvg from "../public/svg/circleCat.svg"
import achiv1 from "../public/svg/achiv123.svg"
import Avatar from '@mui/material/Avatar';
import {Grid} from "@material-ui/core";
import ContactsPage from "../components/IndexPage/ContactsPage";
import {styled} from '@mui/material/styles';
import Badge from '@mui/material/Badge';
import {getUserRole, isAdmin} from "../scripts/rolesConfig";
import {
    Button, Checkbox,
    Chip,
    Dialog,
    DialogContent,
    DialogTitle,
    Divider,
    FormControlLabel,
    IconButton,
    Tooltip
} from "@mui/material";
import AssignmentIndIcon from '@mui/icons-material/AssignmentInd';
import {getAchievements} from "../store/slices/achivSlice";
import {getLevelInfo} from "../store/slices/levelSlice";
import VisibilityIcon from "@mui/icons-material/Visibility";
import TopicIcon from "@mui/icons-material/Topic";
import DataArrayIcon from "@mui/icons-material/DataArray";
import {deleteTask, getTasksInfo} from "../store/slices/tasksSlice";
import DeleteIcon from "@mui/icons-material/Delete";
import ExpandLessIcon from "@mui/icons-material/ExpandLess";
import ExpandMoreIcon from "@mui/icons-material/ExpandMore";
import Markdown from "../components/Markdown/Markdown";
import {getTeacherUsers} from "../store/slices/generalSlice";
import {getTasksTeacherInfo} from "../store/slices/teacherTasksSlice";
import EditIcon from "@mui/icons-material/Edit";
import {useNavigate} from "react-router-dom";
import CloseIcon from "@mui/icons-material/Close";
import AddTaskIcon from "@mui/icons-material/AddTask";

function PersonalInformation({data, onChange}) {
    const {handleSubmit, control} = useForm({
        mode: 'all',
        shouldUnregister: true
    });
    const dispatch = useDispatch();

    const onSubmit = (data) => {
        dispatch(updateUserInfo(data));
        onChange();
    }

    return (
        <form onSubmit={handleSubmit(onSubmit)} className={s.form}>
            <div className={s.inputs_container}>
                <Controller
                    classname={s.input}
                    name={'login'}
                    defaultValue={data?.login}
                    control={control}
                    render={({field}) => (
                        <ProfileInput
                            {...field}
                            id={'login-input'}
                            type={'text'}
                            label={'Логин'}/>
                    )}/>
                <Controller
                    name={'full_name'}
                    defaultValue={data?.full_name}
                    control={control}
                    render={({field}) => (
                        <ProfileInput
                            {...field}
                            id={'fullName-input'}
                            type={'text'}
                            label={'ФИО'}/>
                    )}/>
                <Controller
                    name={'email'}
                    defaultValue={data?.email}
                    control={control}
                    render={({field}) => (
                        <ProfileInput
                            {...field}
                            id={'email-input'}
                            type={'text'}
                            label={'Электронная почта'}/>
                    )}/>
                <input className={s.btn} type={'submit'} value={'Сохранить'}/>
            </div>
        </form>
    );
}

function Security({onChange}) {
    const {handleSubmit, getValues, control, formState: {errors}} = useForm({
        mode: 'all',
        shouldUnregister: true
    });
    const dispatch = useDispatch();

    const onSubmit = (data) => {
        dispatch(updateUserInfo(data));
        onChange();
    }

    return (
        <form onSubmit={handleSubmit(onSubmit)} className={s.safetyForm}>
            <Controller
                name={'oldPassword'}
                control={control}
                render={({field}) => (
                    <ProfileInput
                        {...field}
                        id={'oldPassword-input'}
                        type={'password'}
                        label={'Старый пароль'}/>
                )}/>
            <Controller
                name={'newPassword'}
                control={control}
                render={({field}) => (
                    <ProfileInput
                        {...field}
                        id={'newPassword-input'}
                        type={'password'}
                        label={'Новый пароль'}/>
                )}/>
            <Controller
                name={'confirmNewPassword'}
                control={control}
                rules={{validate: value => value === getValues('newPassword')}}
                render={({field}) => (
                    <ProfileInput
                        {...field}
                        id={'confirmNewPassword-input'}
                        type={'password'}
                        label={'Подтвердите новый пароль'}/>
                )}/>
            {errors.confirmNewPassword && <span className={s.warning}>Пароли должны совпадать</span>}
            <input className={s.btn} type={'submit'} value={'Сохранить'}/>
        </form>
    );
}

export default function profile() {
    const Pages = {personalInfo: 'personalInfo', security: 'security'}
    const [settingsPage, setSettingsPage] = useState(Pages.personalInfo);
    const dispatch = useDispatch();
    const user = useSelector(state => state.user.info);
    const level = useSelector(state => state.level.info.data)
    const achievements = useSelector(state => state.achievements.info);
    const userAvatar = useSelector(state => state.user.avatar)
    const tasks = useSelector(state => state.tasksTeacher.info);

    useEffect(() => {
        dispatch(getUserInfo());
        dispatch(getAchievements());
        dispatch(getLevelInfo());
        dispatch(getTasksTeacherInfo());
        // dispatch(getSelfUserAvatar());
    }, []);

    function onChangeHandler() {
        dispatch(getUserInfo());
    }
    const navigate = useNavigate();

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
            <div style={{
                width: '100%',
            }}>
                <Grid
                    container
                    spacing={0}
                    style={{
                        width: '100%',
                        padding: '10px',
                        borderRadius: '8px',
                        marginTop: '10px',
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
                    <Grid item xs={12} sm={3} md={3} lg={3} className={s.fio}>
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
                    <Grid item xs={12} sm={3} md={3} lg={3} className={s.topic}>
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
                    <Grid container item xs={8} sm={2} md={2} lg={2}
                          style={{
                              justifyContent: 'right'
                          }}>
                        <Grid item xs={6} sm={6} md={4} lg={3}>
                            <GoEditTask task={task} navigate={navigate}/>
                        </Grid>
                        <Grid item xs={6} sm={6} md={4} lg={3}>
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
                    <Grid container item spacing={0} xs={4} sm={1} md={1} lg={1} className={s.buttons}>
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

    function stringToColor(string) {
        let hash = 0;
        let i;

        /* eslint-disable no-bitwise */
        for (i = 0; i < string.length; i += 1) {
            hash = string.charCodeAt(i) + ((hash << 5) - hash);
        }

        let color = '#';

        for (i = 0; i < 3; i += 1) {
            const value = (hash >> (i * 8)) & 0xff;
            color += `00${value.toString(16)}`.slice(-2);
        }
        /* eslint-enable no-bitwise */

        return color;
    }

    const StyledBadge = styled(Badge)(({theme}) => ({
        '& .MuiBadge-badge': {
            backgroundColor: '#44b700',
            color: '#44b700',
            boxShadow: `0 0 0 2px ${theme.palette.background.paper}`,
            '&::after': {
                position: 'absolute',
                top: 0,
                left: 0,
                width: '100%',
                height: '100%',
                borderRadius: '50%',
                animation: 'ripple 1.2s infinite ease-in-out',
                border: '1px solid currentColor',
                content: '""',
            },
        },
        '@keyframes ripple': {
            '0%': {
                transform: 'scale(.8)',
                opacity: 1,
            },
            '100%': {
                transform: 'scale(2.4)',
                opacity: 0,
            },
        },
    }));
    const [filters, setFilters] = useState({
        tasks: []
    });

    function filtering(tasks) {
        let result = tasks;
        let filteredItems = [];

        if (filters.tasks.length !== 0) {
            for (let i = 0; i < filters.tasks.length; i++) {
                filteredItems = filteredItems.concat(
                    result.filter(tasks =>
                        tasks?.data?.id === filters.tasks[i].id
                    )
                );
            }

            result = filteredItems;
        }

        return result;
    }

    const ProgressBar = (props) => {
        const {progress, naw} = props
        const prog = 160 / progress;
        return (
            <div className={s.progressBar} style={{width: `160px`}}>
                <div
                    className={s.progressBarFill}
                    style={{width: `${prog * naw}px`}}
                ></div>
            </div>
        );
    };
    return (
        <Page pageTitle={'Профиль'}>
            <div className={s.backgroundStyle}>
                <div className={s.ctn}>
                    <Grid container className={s.contentName}>
                        <Grid container item xs={12} sm={12} md={8} lg={9} className={s.hello}>
                            <Grid item xs={4} sm={4} md={4} lg={4}>
                                <div className={s.catSVG} dangerouslySetInnerHTML={{__html: catSvg}}/>
                            </Grid>
                            <Grid item xs={7} sm={7} md={7} lg={7}>
                                <div>
                                    <h1 className={s.lableText}>Привет!</h1>
                                    <h4 className={s.h4Text}>Мы ждем твоих новых свершений!</h4>
                                </div>
                                {user?.data?.roles[0] === "ROLE_USER" ?
                                    level?.current_level?.name ? (
                                            <div style={{color: 'yellow', marginTop: '30px'}}>
                                                <div className={s.level} style={{display: 'inline-block'}}
                                                     dangerouslySetInnerHTML={{__html: levelSVG}}/>
                                                <div style={{display: 'inline-block'}}>
                                                    <div style={{
                                                        backgroundColor: 'rgba(0,31,129,0.2)',
                                                        borderRadius: '16px',
                                                        padding: '4px 10px',
                                                        width: '124px'
                                                    }}>
                                                        {level.current_level.name} </div>
                                                    <div style={{display: 'inline-block'}}>
                                                        <ProgressBar progress={level.next_level.need_scores}
                                                                     naw={level.scores}/>
                                                    </div>
                                                    <div
                                                        style={{
                                                            position: 'relative', display: 'inline-block',
                                                            margin: '0 10px',
                                                            top: '-4px'
                                                        }}>{level.scores}/{level.next_level.need_scores}</div>
                                                    <div style={{
                                                        width: '26px',
                                                        display: 'inline-block'
                                                    }} dangerouslySetInnerHTML={{__html: starSVG}}/>
                                                </div>
                                            </div>
                                        ) :
                                        (<div>Загрузка </div>)
                                    : (<div></div>)}
                            </Grid>
                        </Grid>
                        <Grid item xs={12} sm={12} md={4} lg={3} className={s.user}>
                            {
                                user.isLoading
                                    ?
                                    <ProfileHeaderSkeleton/>
                                    :
                                    <>
                                        <StyledBadge
                                            overlap="circular"
                                            anchorOrigin={{vertical: 'bottom', horizontal: 'right'}}
                                            variant="dot"
                                            className={s.user__avatar}
                                        >
                                            <Avatar
                                                className={s.true__icon}
                                                src={user?.data?.avatar_base64}
                                            />
                                        </StyledBadge>
                                        <div className={s.user__main_info}>
                                            <span className={s.user__fullname}>{user.data?.full_name}</span>
                                            <Chip
                                                className={s.user__role}
                                                icon={<AssignmentIndIcon/>}
                                                label={user.data ? getUserRole(user.data?.roles).label : ''}
                                            />
                                        </div>
                                    </>
                            }
                        </Grid>
                        <Grid container item className={s.achivBack} xs={12} sm={12} md={12} lg={12}>

                            <Grid container item style={{
                                padding: `20px 20px 0 20px`,
                                justifyContent: 'center',
                                color: 'white',
                                width: '100%',
                                fontSize: '28px',
                                fontWeight: 'bold'
                            }
                            } xs={12} sm={12} md={12} lg={12}>{
                                user?.data?.roles[0] === "ROLE_USER" ?
                                    (<div>Достижения
                                        <div style={{
                                            width: '46px',
                                            display: 'inline-block',
                                            marginLeft: '10px'
                                        }} dangerouslySetInnerHTML={{__html: achiv1}}/>
                                    </div>)
                                    : (<div >
                                        <div style={{
                                            display: 'inline-block',
                                        }}>Мои задания
                                        </div>

                                        <div style={{
                                            display: 'inline-block',
                                            marginLeft: '20px'
                                        }}>
                                            <Tooltip title="Создать задание">
                                                <IconButton
                                                    aria-label="more"
                                                    label="gg"
                                                    href="/tasks/createTask"
                                                >
                                                    <AddTaskIcon/>
                                                </IconButton>
                                            </Tooltip>
                                        </div>
                                    </div>)
                            }

                            </Grid>

                            <Grid container item className={s.achiv} xs={12} sm={12} md={12} lg={12}>

                                {
                                    user?.data?.roles[0] === "ROLE_USER" ?
                                        achievements?.data ?
                                            filtering(achievements?.data).map((achievement) => (
                                                    (achievement.progress.need_score === achievement.progress.current_score) ?
                                                        (
                                                            <Grid item xs={4} sm={3} md={2} lg={2} key={achievement.id}
                                                                  className={s.achive}
                                                                  style={{
                                                                      color: 'white',
                                                                      justifyContent: `center`
                                                                  }}>
                                                                <img style={{
                                                                    borderRadius: '10%',
                                                                    borderStyle: 'solid',
                                                                    borderColor: 'white',
                                                                    borderWidth: '2px',
                                                                    maxWidth: '60px'

                                                                }} src={achievement.achievement.image_href}
                                                                     alt="описание изображения"/>
                                                                <div
                                                                    className={s.progress}>{achievement.progress.need_score}/{achievement.progress.current_score}</div>
                                                                <div className={s.description}>
                                                                    <div style={{
                                                                        fontWeight: '460',
                                                                        marginBottom: '4px'
                                                                    }}>{achievement.achievement.name}</div>
                                                                    <div>{achievement.achievement.description}</div>
                                                                </div>
                                                            </Grid>)
                                                        :
                                                        (
                                                            <Grid container item xs={2} sm={1} md={1} lg={1}
                                                                  key={achievement.id} className={s.achive}
                                                                  style={{
                                                                      color: 'white',
                                                                  }}>
                                                                <img style={{
                                                                    borderRadius: '10%',
                                                                    maxWidth: '60px'

                                                                }} src={achievement.achievement.image_href}
                                                                     alt="описание изображения"/>
                                                                <div
                                                                    className={s.progress}>{achievement.progress.need_score}/{achievement.progress.current_score}</div>
                                                            </Grid>)

                                                )
                                            )
                                            : "Loading..."
                                        : (
                                            <div style={{
                                                width: '100%'
                                            }}>
                                                {
                                                    tasks?.data ?
                                                        filtering(tasks?.data)?.map((task) => (
                                                                <ListTask key={task.id} task={task}/>
                                                            )
                                                        )
                                                        : "Loading..."
                                                }
                                            </div>)
                                }

                            </Grid>
                        </Grid>
                    </Grid>

                    {/*    <div className={classNames(s.content, {[s.loading]: user.isLoading})}>*/}
                    {/*    {*/}
                    {/*        user.isLoading*/}
                    {/*            ?*/}
                    {/*            <div className={s.form}>*/}
                    {/*                <FormSkeleton/>*/}
                    {/*            </div>*/}
                    {/*            :*/}
                    {/*            <>*/}
                    {/*                {settingsPage === Pages.personalInfo &&*/}
                    {/*                    <PersonalInformation data={user.data} onChange={() => onChangeHandler()}/>}*/}
                    {/*                {settingsPage === Pages.security && <Security onChange={() => onChangeHandler()}/>}*/}
                    {/*            </>*/}
                    {/*    }*/}

                    {/*    <div className={s.menu}>*/}
                    {/*        {*/}
                    {/*            user.isLoading*/}
                    {/*                ?*/}
                    {/*                <VerticalMenuSkeleton/>*/}
                    {/*                :*/}
                    {/*                <VerticalMenu*/}
                    {/*                    header={'Настройки аккаунта'}*/}
                    {/*                    onChange={item => setSettingsPage(item)}*/}
                    {/*                    selected={settingsPage}*/}
                    {/*                    position={'right'}>*/}
                    {/*                <span*/}
                    {/*                    className={classNames(s.item, {[s.selected]: settingsPage === Pages.personalInfo})}*/}
                    {/*                    onClick={() => {*/}
                    {/*                        setSettingsPage(Pages.personalInfo)*/}
                    {/*                    }}>*/}
                    {/*                        Личная информация*/}
                    {/*                </span>*/}
                    {/*                    <span*/}
                    {/*                        className={classNames(s.item, {[s.selected]: settingsPage === Pages.security})}*/}
                    {/*                        onClick={() => {*/}
                    {/*                            setSettingsPage(Pages.security)*/}
                    {/*                        }}>*/}
                    {/*                        Изменить пароль*/}
                    {/*                </span>*/}
                    {/*                </VerticalMenu>*/}
                    {/*        }*/}
                    {/*    </div>*/}
                    {/*</div>*/}

                </div>
                <div className={s.contact}>
                    <ContactsPage/>
                </div>

            </div>
        </Page>
    )
        ;
}