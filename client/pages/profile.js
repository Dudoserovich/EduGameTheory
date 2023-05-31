import React, {useEffect, useState} from 'react';
import s from '../styles/pages/profile.module.scss';
import Page from "../layout/Page/Page";
import {Controller, useForm} from "react-hook-form";
import Input from '../components/Input/Input';
import {getUserAvatar, getUserInfo, updateUserInfo} from '../store/slices/userSlice';
import {useDispatch, useSelector} from 'react-redux';
import {FormSkeleton, ProfileHeaderSkeleton, VerticalMenuSkeleton} from '../components/Skeletons/ProfileSkeleton';
import VerticalMenu from '../components/VerticalMenu/VerticalMenu';
import {AiOutlineUser} from "react-icons/ai";
import classNames from 'classnames';
import ProfileInput from "../components/Input/ProfileInput";
import catSvg from  "../public/svg/circleCat.svg"
import Avatar from '@mui/material/Avatar';
import Stack from '@mui/material/Stack';
import {Grid} from "@material-ui/core";
import BoxAnimation from "../components/BoxAnimation/BoxAnimation";
import ContactPage from "../components/IndexPage/ContactsPage";
import {Button} from "@mui/material";
import {getRequest} from "../api";
import CONFIG from "../config";
import {solvePayoff} from "../store/slices/taskSolveSlice";

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
    const userAvatar = useSelector(state => state.user.avatar)
    // TODO: Решение матричной игры
    // const taskPayoff = useSelector(state => state.task.info)

    useEffect(() => {
        dispatch(getUserInfo());
        dispatch(getUserAvatar());
        // TODO: Решение матричной игры
        // dispatch(solvePayoff(
        //     {
        //         strategy: "смешанные стратегии",
        //         first_player: [
        //             0.8,
        //             0.19999999999999996
        //         ],
        //         second_player: [
        //             0.3999999999999999,
        //             0.6000000000000001
        //         ],
        //         game_price: 4.6
        //     })
        // )
    }, []);

    // TODO: Решение матричной игры
    // console.log(taskPayoff)

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

    function stringAvatar(name) {
        return {
            sx: {
                bgcolor: stringToColor(name),
            },
            children: `${name.split(' ')[0][0]}${name.split(' ')[1][0]}`,
        };
    }

    return (
        <Page pageTitle={'Профиль'}>
            <div className={s.backgroundStyle}>
                <div className={s.ctn}>
                    <Grid container className={s.contentName}>
                        <Grid container xs={12} sm={12} md={8} lg={9} className={s.hello}>
                            <Grid item xs={4} sm={4} md={4} lg={4}>
                                <div className={s.catSVG} dangerouslySetInnerHTML={{__html: catSvg}}/>
                            </Grid>
                            <Grid item xs={7} sm={7} md={7} lg={7}>
                                <div>
                                    <h1 className={s.lableText}>Привет!</h1>
                                    <h4 className={s.h4Text}>Мы ждем твоих новых свершений!</h4>
                                </div>
                            </Grid>

                        </Grid>
                        <Grid item xs={12} sm={12} md={4} lg={3} className={s.user}>
                            {
                                user.isLoading
                                    ?
                                    <ProfileHeaderSkeleton/>
                                    :
                                    <>
                                        <div className={s.user__avatar}>
                                        {user.data?.full_name ?
                                            <Avatar
                                                className={s.true__icon}
                                                src={userAvatar?.data}
                                            />
                                            : <AiOutlineUser className={s.fake__icon}/>
                                        }
                                        </div>
                                        {/* TODO: ДЛЯ НАСТИ - вот так может выглядеть ссылка для НЕ ТЕКУЩЕГО пользователя*/}
                                        {/*<div>*/}
                                        {/*    <img className={s.fake__icon} src={user.data?.avatar}></img>*/}
                                        {/*</div>*/}
                                        <div className={s.user__main_info}>
                                            <span className={s.user__fullname}>{user.data?.full_name}</span>
                                        </div>
                                    </>
                            }
                        </Grid>
                    </Grid>

                    {/*                <div className={classNames(s.content, {[s.loading]: user.isLoading})}>
                    {
                        user.isLoading
                            ?
                            <div className={s.form}>
                                <FormSkeleton/>
                            </div>
                            :
                            <>
                                {settingsPage === Pages.personalInfo &&
                                    <PersonalInformation data={user.data} onChange={() => onChangeHandler()}/>}
                                {settingsPage === Pages.security && <Security onChange={() => onChangeHandler()}/>}
                            </>
                    }

                    <div className={s.menu}>
                        {
                            user.isLoading
                                ?
                                <VerticalMenuSkeleton/>
                                :
                                <VerticalMenu
                                    header={'Настройки аккаунта'}
                                    onChange={item => setSettingsPage(item)}
                                    selected={settingsPage}
                                    position={'right'}>
                                <span
                                    className={classNames(s.item, {[s.selected]: settingsPage === Pages.personalInfo})}
                                    onClick={() => {
                                        setSettingsPage(Pages.personalInfo)
                                    }}>
                                        Личная информация
                                </span>
                                    <span
                                        className={classNames(s.item, {[s.selected]: settingsPage === Pages.security})}
                                        onClick={() => {
                                            setSettingsPage(Pages.security)
                                        }}>
                                        Изменить пароль
                                </span>
                                </VerticalMenu>
                        }
                    </div>
                </div>*/}

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
                <div className={s.contact}>
                    <ContactPage/>
            </div>

            </div>
        </Page>
    );
}