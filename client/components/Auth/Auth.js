import React, {FC, useEffect} from 'react';
import s from './Auth.module.scss';
import {Controller, useForm} from "react-hook-form";
import Input from '../Input/Input';
import router from '../../polyfills/router';
import Spinner from '../Spinner/Spinner';
// import {MainLogo} from '../../public/logos/Logos';
import {useDispatch, useSelector} from 'react-redux';
import {getToken, saveToken} from '../../store/slices/authSlice';
import Button from '../Button';
import {refreshingToken} from '../../api';
import {getRefreshToken} from '../../scripts/jwtService';
import CONFIG from "../../config";
import {Accordion, AccordionSummary, Typography} from "@material-ui/core";
import ExpandMoreIcon from '@mui/icons-material/ExpandMore';
import {AccordionDetails, Card, CardActions, CardContent} from "@mui/material";
import Dropdown from "../Dropdown/Dropdown";
import {MainLogo} from "../../public/logos/Logos";

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
        <div style={{
            display: 'flex',
            alignItems: 'baseline',
            justifyContent: 'space-between',
            width: '100%',
            padding: '0 16px',
            flexDirection: 'column'
        }}>
            <div style={{
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'space-between',
                width: '100%',
                height: 'auto',
                padding: '0px 16px',
                alignContent: 'center'
            }}>
                <div style={{
                    display: 'flex',
                    color: 'black',
                    alignItems: 'center',
                    width: '12em',
                    justifyContent: 'space-between'
                }}>
                    <MainLogo style={{width: '35px', height: '35px'}}/>
                    <Typography sx={{fontSize: 18}} color="text.secondary" gutterBottom>
                        EduGameTheory
                    </Typography>
                </div>

                <Accordion>
                    <AccordionSummary
                        expandIcon={<ExpandMoreIcon/>}
                        aria-controls="panel1bh-content"
                        id="panel1a-header"
                    >
                        <Typography>Авторизация</Typography>
                    </AccordionSummary>
                    <AccordionDetails>
                        <form onSubmit={handleSubmit(onSubmit)}>
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
                                            placeholder={"Логин"}/>
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
            <Card sx={{minWidth: 350}}
                  style={{
                      width: 'auto%',
                      display: 'flex',
                      flexDirection: 'column',
                      // alignItems: 'center',
                      alignSelf: 'center',
                      margin: '5%',
                      alignItems: 'stretch'
                  }}
            >
                <CardContent>
                    <Typography sx={{fontSize: 14}} color="text.secondary" gutterBottom>
                        Word of the Day
                    </Typography>
                    <Typography sx={{mb: 1.5}} color="text.secondary">
                        adjective
                    </Typography>
                    <Typography variant="body2">
                        well meaning and kindly.
                        <br/>
                        {'"a benevolent smile"'}
                    </Typography>
                </CardContent>
                <CardActions>
                    <Button size="small">Learn More</Button>
                </CardActions>
            </Card>
        </div>

        // <div className={s.ctn}>
        //     <div className={s.logo_container}>
        //         <MainLogo className={s.logo}/
        //         <span className={s.name}>EduGameTheory</span>
        //     </div>
        //     <div className={s.form_content}>
        //         <form onSubmit={handleSubmit(onSubmit)} className={s.form}>
        //             <div className={s.input_container}>
        //                 <Controller
        //                     name="username"
        //                     control={control}
        //                     rules={{required: true}}
        //                     render={({field}) => (
        //                         <Input
        //                             {...field}
        //                             id={"login-input"}
        //                             type={"text"}
        //                             placeholder={"Логин"}/>
        //                     )}/>
        //                 {errors.username && <span className={s.warning}>Обязательное поле</span>}
        //             </div>
        //             <div className={s.input_container}>
        //                 <Controller
        //                     name="password"
        //                     control={control}
        //                     rules={{required: true}}
        //                     render={({field}) => (
        //                         <Input
        //                             {...field}
        //                             id={"password-input"}
        //                             type={"password"}
        //                             placeholder={"Пароль"}/>
        //                     )}/>
        //                 {errors.password && <span className={s.warning}>Обязательное поле</span>}
        //             </div>
        //             {
        //                 authState.isLoading
        //                     ?
        //                     <Spinner/>
        //                     :
        //                     <Button type={'submit'} className={s.button}>Войти</Button>
        //             }
        //         </form>
        //     </div>
        //     <div className={s.waves_container}>
        //         <Wave
        //             className={s.wave}
        //             fill={'#db001b'}
        //             paused={false}
        //             options={{
        //                 height: 20,
        //                 amplitude: waveAmplitude / 2,
        //                 speed: 0.3,
        //                 points: wavePoints
        //             }}/>
        //         <Wave
        //             className={s.wave}
        //             fill={'#DB4246'}
        //             paused={false}
        //             options={{
        //                 height: 20,
        //                 amplitude: waveAmplitude,
        //                 speed: 0.2,
        //                 points: wavePoints - 1
        //             }}/>
        //     </div>
        // </div>
    );
}

export default Auth;
