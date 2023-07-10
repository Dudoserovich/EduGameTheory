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
import {getUserRole} from "../scripts/rolesConfig";
import {Chip} from "@mui/material";
import AssignmentIndIcon from '@mui/icons-material/AssignmentInd';
import {getAchievements} from "../store/slices/achivSlice";
import {getLevelInfo} from "../store/slices/levelSlice";

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
    console.log(achievements)
    useEffect(() => {
        dispatch(getUserInfo());
        dispatch(getAchievements());
        dispatch(getLevelInfo());
        // dispatch(getSelfUserAvatar());
    }, []);

    function onChangeHandler() {
        dispatch(getUserInfo());
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
                                {
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
                                        (<div>Загрузка </div>)}
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
                                padding: `20px`,
                                marginBottom: '20px',
                                justifyContent:'center',
                                color: 'white',
                                width: '100%',
                                fontSize: '28px',
                                fontWeight: 'bold'
                            }
                            } xs={12} sm={12} md={12} lg={12}>Достижения
                               <div style={{
                                   width: '46px',
                                   display: 'inline-block',
                                   marginLeft: '10px'
                               }} dangerouslySetInnerHTML={{__html: achiv1}}/>

                            </Grid>

                            <Grid container item className={s.achiv} xs={12} sm={12} md={12} lg={12}>

                                {
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