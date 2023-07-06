import React, {useEffect} from 'react';
import s from '../../styles/tasks/task.module.scss';
import Page from "../../layout/Page/Page";
import {useDispatch, useSelector} from 'react-redux';
import {Grid} from "@material-ui/core";
import {useLocation} from 'react-router-dom';
import check from "../../public/svg/check.svg";
import {getPlayInfo} from "../../store/slices/taskPlaySlice";
import {TaskPlayPayoff} from "../../store/slices/taskPlayGameSlice";
import Markdown from "../../components/Markdown/Markdown";
import Matrix from "../../components/Matrix/Matrix";


export default function TasksPlay() {
    const taskPlay = useSelector(state => state.taskPlay.info);
    const playGame = useSelector(state => state.playGame.info);
    const dispatch = useDispatch();

    const {state} = useLocation();
    const task = state.task;

    useEffect(() => {
        dispatch(getPlayInfo({id: task.id}));
    }, []);
    console.log(playGame)

    const onClickPlay = (index) => {
        const TasksPayoffClear = {
            row_number: index,
        }
        console.log(TasksPayoffClear)
        dispatch(TaskPlayPayoff({id: task.id, IData: TasksPayoffClear}));
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
                        <Grid item xs={12} sm={12} md={12} lg={12}>
                            <Markdown
                                className={s.descriptionsR}
                                value={task?.description.trim()}
                            />
                        </Grid>
                        <Grid item xs={12} sm={12} md={12} lg={12} className={s.title}>
                            Подсказки
                        </Grid>
                        <Grid item xs={12} sm={12} md={12} lg={12}>
                            {taskPlay?.data?.description ?
                                (
                                    <Markdown
                                        className={s.descriptionsR}
                                        value={taskPlay?.data?.description}
                                    />
                                )
                                : (<div>Загрузка</div>)
                            }
                        </Grid>
                        <Grid item xs={12} sm={10} md={10} lg={10} className={s.descriptionsR}>
                            Помните что строки матрицы - это стратегии 1-го игрока, а столбцы - это стратегии
                            второго игрока.
                        </Grid>
                        <Grid container item xs={12} sm={6} md={6} lg={6}>
                            <Grid item xs={12} sm={12} md={12} lg={12} className={s.title}>
                                Матрица
                            </Grid>
                            <Grid item xs={12} sm={12} md={12} lg={12}
                                  style={{maxWidth: "fit-content"}}
                            >
                                <Matrix matrix={task?.matrix}/>
                            </Grid>
                        </Grid>
                        <Grid container item xs={12} sm={6} md={6} lg={6}>
                            <Grid item xs={12} sm={12} md={12} lg={12} className={s.title}>
                                Стратегии игроков
                            </Grid>
                            <Grid item xs={12} sm={12} md={12} lg={12}
                                style={{maxWidth: "fit-content"}}
                            >
                                {taskPlay?.data?.chance_first ?
                                    (<table className={s.backgroundMatrix}>
                                        <caption>
                                            Вероятности первого игрока:
                                        </caption>
                                        <tbody>
                                        <tr>{
                                            taskPlay.data.chance_first.map((row) => (
                                                <td className={s.col}>{row.toFixed(4)}</td>
                                            ))}</tr>
                                        </tbody>
                                    </table>)
                                    : (<div>Загрузка</div>)
                                }
                            </Grid>
                            <Grid item xs={12} sm={12} md={12} lg={12}
                                  style={{maxWidth: "fit-content"}}
                            >
                                {taskPlay?.data?.chance_second ?
                                    (<table className={s.backgroundMatrix}>
                                        <caption>
                                            Вероятности второго игрока:
                                        </caption>
                                        <tbody>
                                        <tr>{
                                            taskPlay.data.chance_second.map((row) => (
                                                <td className={s.col}>{row.toFixed(4)}</td>
                                            ))}</tr>
                                        </tbody></table>)
                                    : (<div>Загрузка</div>)
                                }
                            </Grid>{/*
                            {playGame?.data?.your_chance ?
                                (<Grid item xs={12} sm={12} md={12} lg={12}>
                                Ваш шанс:{
                                    playGame.data.your_chance.map((row) => (
                                        <div>{row},</div>
                                    ))}
                            </Grid>)
                                : (<Grid item xs={12} sm={12} md={12} lg={12}></Grid>)}*/}
                        </Grid>
                        {playGame?.data?.moves ?
                            (<Grid item xs={12} sm={12} md={12} lg={12}>
                                Прошлый шаг:{
                                playGame.data.moves.map((row) => (
                                    <div>{row},</div>
                                ))}
                            </Grid>)
                            : (<Grid item xs={12} sm={12} md={12} lg={12}></Grid>)}
                        {playGame?.data?.result_move ?
                            (<Grid item xs={12} sm={12} md={12} lg={12}>
                                Результат:{playGame.data.result_move}
                            </Grid>)
                            : (<Grid item xs={12} sm={12} md={12} lg={12}></Grid>)}
                        <Grid container item xs={12} sm={6} md={4} lg={4}>
                            <Grid item xs={12} sm={12} md={12} lg={12} className={s.title}>
                                Решение
                            </Grid>
                            <Grid item xs={12} sm={12} md={12} lg={12} className={s.title}>
                                {task.matrix.map((row, rowIndex) => (
                                    <div key={rowIndex}>
                                        <button onClick={() => {
                                            onClickPlay(rowIndex)
                                        }}>Выбрать {rowIndex + 1} стратегию
                                        </button>
                                    </div>
                                ))}
                            </Grid>
                        </Grid>

                    </Grid>

                </div>
            </div>
        </Page>
    );
}