import React, {useEffect} from "react";
import s from './Button.module.scss';
import {DialogTitle, Dialog, DialogContent, Button} from "@mui/material";
import {Controller, useForm} from "react-hook-form";
import Input from "../Input/Input";
import Spinner from "../Spinner/Spinner";
import {getRefreshToken} from "../../scripts/jwtService";
import {refreshingToken} from "../../api";
import router from "router";
import {useDispatch, useSelector} from "react-redux";
import {getToken, saveToken} from "../../store/slices/authSlice";
import closeSvg from '../../public/svg/close.svg';

export default function ProjectPage() {
    const [open, setOpen] = React.useState( false);

    const handleClickOpen = () => {
        setOpen(true);
    };
    const handleClose = () => {
        setOpen(false);
    }

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
        <>
            <button className={s.botton} onClick={handleClickOpen}>Начать</button>
            <Dialog open={open} onClose={handleClose} aria-lablledby='form-dialog-title' fullWidth={true} >
                <DialogTitle id='form-dialog-title' className={s.back}>
                    <Button onClick={handleClose}>
                        <div className={s.closeSVG} dangerouslySetInnerHTML={{__html: closeSvg}}/>
                    </Button>
                    <h1 className={s.title}>Авторизация</h1>
                </DialogTitle>
                <DialogContent className={s.contents}>
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
                                        placeholder={"Пароль"}
                                        className={s.input}/>
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
                                <Button type={'submit'} className={s.bottonGo}>Войти</Button>
                        }
                    </form>
                </DialogContent>
            </Dialog>
        </>
    )
}