import React, {FC, useEffect} from 'react';
import s from './Auth.module.scss';
import {Controller, useForm} from "react-hook-form";
import Input from '../Input/Input';
import router from '../../polyfills/router';
import Spinner from '../Spinner/Spinner';
import {useDispatch, useSelector} from 'react-redux';
import {getToken, saveToken} from '../../store/slices/authSlice';
import Button from '../Button';
import {refreshingToken} from '../../api';
import {getRefreshToken} from '../../scripts/jwtService';
import {Accordion, AccordionSummary, Typography} from "@material-ui/core";
import ExpandMoreIcon from '@mui/icons-material/ExpandMore';
import {AccordionDetails, Card, CardActions, CardContent} from "@mui/material";


export var user = 'lead';

const Auth = () => {
    const {handleSubmit, control, formState: {errors}} = useForm({
        mode: 'onBlur',
        defaultValues: {
            username: '',
            password: ''
        }
    });

    useEffect(() => {
        let refreshToken = getRefreshToken();
        if (refreshToken) {
            refreshingToken()
                .then(() => {
                    router.push('/profile');
                })
        }
    }, [])

    const dispatch = useDispatch();
    const authState = useSelector(state => state.auth.authInfo);

    const onSubmit = (data) => {
        dispatch(getToken(data));
    }

    if (authState.data) {
        dispatch(saveToken());
        router.push('/profile');
    }

    return (
        <div>
            <Accordion className={s.auth}>
                <AccordionSummary
                    expandIcon={<ExpandMoreIcon/>}
                    aria-controls="panel1a-content"
                    id="panel1a-header"
                    className={s.authSummary}
                >
                    <Typography>Авторизация</Typography>
                </AccordionSummary>
                <AccordionDetails  className={s.authDetails}>
                    <form onSubmit={handleSubmit(onSubmit)} >
                        <div>
                            <Controller
                                name="username"
                                control={control}
                                rules={{required: true}}
                                render={({field}) => (
                                    <Input
                                        {...field}
                                        id={"login-input"}
                                        type={"text"}
                                        placeholder={"Логин"}
                                        className={s.input}/>

                                )}/>
                            {errors.username &&
                                <span style={{
                                    color: 'var(--main-brand-color)'
                                }}>Обязательное поле</span>}
                        </div>
                        <div>
                            <Controller
                                name="password"
                                control={control}
                                rules={{required: true}}
                                render={({field}) => (
                                    <Input
                                        {...field}
                                        id={"password-input"}
                                        type={"password"}
                                        placeholder={"Пароль"}/>
                                )}/>
                            {errors.password && <span style={{
                                color: 'var(--main-brand-color)'
                            }}>Обязательное поле</span>}
                        </div>
                        {
                            authState.isLoading
                                ?
                                <Spinner/>
                                :
                                <Button type={'submit'} className={s.button}>Войти</Button>
                        }
                    </form>
                </AccordionDetails>
            </Accordion>
        </div>
    );
}

export default Auth;
