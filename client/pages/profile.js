import React, {useEffect, useState} from 'react';
import s from '../styles/pages/profile.module.scss';
import Page from "../layout/Page/Page";
import {Controller, useForm} from "react-hook-form";
import Input from '../components/Input/Input';
import {getUserInfo, updateUserInfo} from '../store/slices/userSlice';
import {useDispatch, useSelector} from 'react-redux';
import {FormSkeleton, ProfileHeaderSkeleton, VerticalMenuSkeleton} from '../components/Skeletons/ProfileSkeleton';
import VerticalMenu from '../components/VerticalMenu/VerticalMenu';
import {AiOutlineUser} from "react-icons/ai";
import classNames from 'classnames';
import ProfileInput from "../components/Input/ProfileInput";

import Avatar from '@mui/material/Avatar';
import Stack from '@mui/material/Stack';

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

    useEffect(() => {
        dispatch(getUserInfo());
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
            <div className={s.ctn}>
                <div className={s.user}>
                    {
                        user.isLoading
                            ?
                            <ProfileHeaderSkeleton/>
                            :
                            <>
                                {/*<div className={s.user__avatar}>*/}
                                    {user.data?.full_name ?
                                        <Avatar style={{height: '55px', width: '55px'}} {...stringAvatar(user.data?.full_name)} />
                                        : <AiOutlineUser className={s.icon}/>
                                    }
                                {/*</div>*/}
                                <div className={s.user__main_info}>
                                    <span className={s.user__fullname}>{user.data?.full_name}</span>
                                </div>
                            </>
                    }
                </div>


                <div className={classNames(s.content, {[s.loading]: user.isLoading})}>
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
                </div>
            </div>
        </Page>
    );
}