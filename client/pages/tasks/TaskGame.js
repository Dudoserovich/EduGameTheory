import React, {useEffect, useState} from 'react';
import s from '../../styles/tasks/task.module.scss';
import Page from "../../layout/Page/Page";
import {useDispatch, useSelector} from 'react-redux';
import {Grid} from "@material-ui/core";
import {useLocation} from 'react-router-dom';
import check from "../../public/svg/check.svg";
import {getPlayInfo} from "../../store/slices/taskPlaySlice";
import {TaskPlayPayoff} from "../../store/slices/taskPlayGameSlice";
import Markdown from "../../components/Markdown/Markdown";
import cat from "../../public/svg/cat11.svg";
import winCat from "../../public/svg/catWin.svg";
import cat2 from "../../public/svg/catSink.svg";
import winCat2 from "../../public/svg/CatWin2.svg";
import loseCat from "../../public/svg/catLose.svg";
import loseCat2 from "../../public/svg/cat22.svg";


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

    function Matrix() {
        return (
            <table className={s.backgroundMatrix}>
                <tbody>
                <tr>
                    {
                        (task.name_first_player !== null && task.name_second_player !== null) ?


                            (<td>{task.name_first_player} /<br/>{task.name_second_player}</td>)
                            : (<td></td>)
                    }
                    {
                        (task.name_first_strategies !== null) ?
                            task.name_first_strategies.map((strategies, index) => (

                                (<td key={index} className={s.col}>{strategies}</td>)
                            ))
                            :
                            task.matrix[0].map((strategies, index) => (
                                <td key={index} className={s.col}>{index + 1}-ая стратегия</td>

                            ))
                    }
                </tr>
                {task.matrix.map((row, rowIndex) => (
                    <tr key={rowIndex}>
                        {/* Дополнительная ячейка слева с подписью для каждой строки */}
                        {
                            (task.name_second_strategies !== null) ?
                                (<td key={rowIndex} className={s.col}>{task.name_second_strategies[rowIndex]}</td>)
                                :
                                <td key={rowIndex} className={s.col}>{rowIndex + 1}-ая стратегия</td>
                        }
                        {row.map((cell, cellIndex) => (
                            <td key={cellIndex} className={s.col}>{cell}</td>
                        ))}
                    </tr>
                ))}
                </tbody>
            </table>
        );
    }

    function RandomColorButton(props) {
        const {name, index} = props;
        const [backgroundColor, setBackgroundColor] = useState('');
        useEffect(() => {
            generateRandomColor();
        }, []);

        const generateRandomColor = () => {
            const randomColor = '#' + Math.floor(Math.random() * 16777215).toString(16);
            setBackgroundColor(randomColor);
        };

        return (
            <button className={s.buttons}
                    style={{backgroundColor: backgroundColor}}
                    onClick={() => {
                        onClickPlay(index)
                    }}
            >{name}
            </button>
        );
    }

    const [win, setWin] = useState(0);
    const onClickPlay = (index) => {
        const TasksPayoffClear = {
            row_number: index,
        }
        dispatch(TaskPlayPayoff({id: task.id, IData: TasksPayoffClear}));
        if (playGame.data?.max_score) {
            setWin(playGame.data.max_score / 2);
        }
        console.log(win)
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
                        <Grid item xs={12} sm={12} md={10} lg={10} className={s.descriptionsR}>
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


                        <Grid container item xs={12} sm={12} md={12} lg={12} style={{
                            justifyContent: 'center'
                        }}>
                            <Grid item xs={3} sm={3} md={3} lg={3}>
                                {
                                    win === 0 ? (
                                            <div style={{width: '100%'}} dangerouslySetInnerHTML={{__html: cat2}}/>
                                        ) :
                                        win > playGame.data?.result_move ? (
                                            <div style={{width: '100%'}} dangerouslySetInnerHTML={{__html: winCat2}}/>
                                        ) : win < playGame.data?.result_move ? (
                                                <div style={{width: '100%'}} dangerouslySetInnerHTML={{__html: loseCat2}}/>
                                        ) : (
                                            <div style={{width: '100%'}} dangerouslySetInnerHTML={{__html: winCat2}}/>)
                                }
                            </Grid>
                            <Grid item xs={6} sm={6} md={6} lg={6}>
                                <Grid item xs={12} sm={12} md={6} lg={6}
                                      style={{maxWidth: "fit-content", margin: '20px 10px 0 10px'}}
                                >
                                    {taskPlay?.data?.chance_first ?
                                        (<table className={s.backgroundMatrix}>
                                            <caption>
                                                Ваши вероятности:
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
                                <Grid item xs={12} sm={12} md={6} lg={6}
                                      style={{maxWidth: "fit-content", margin: '20px 10px 0 10px'}}
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
                                            </tbody>
                                        </table>)
                                        : (<div>Загрузка</div>)
                                    }
                                </Grid>
                                <Grid container item xs={12} sm={12} md={12} lg={12} className={s.positionButton}>
                                    {(task.name_first_strategies !== null) ?
                                        task.name_first_strategies.map((row, rowIndex) => (
                                            <div key={rowIndex}>
                                                <RandomColorButton name={row} index={rowIndex}/>
                                            </div>
                                        ))
                                        :
                                        task.matrix.map((row, rowIndex) => (
                                            <div key={rowIndex}>
                                                <RandomColorButton name={(rowIndex + 1).toString() + '-ая стратегия'}
                                                                   index={rowIndex}/>
                                            </div>
                                        ))}
                                </Grid>
                            </Grid>
                            <Grid item xs={3} sm={3} md={3} lg={3}>

                                {
                                    win === 0 ? (
                                            <div style={{width: '100%'}} dangerouslySetInnerHTML={{__html: cat}}/>
                                        ) :
                                    win > playGame.data?.result_move ? (
                                            <div style={{width: '100%'}} dangerouslySetInnerHTML={{__html: loseCat}}/>)
                                         : (
                                        <div style={{width: '100%'}} dangerouslySetInnerHTML={{__html: winCat}}/>
                                    )
                                }
                            </Grid>

                        </Grid>
                        {playGame?.data?.moves ?
                            (<Grid item xs={12} sm={12} md={12} lg={12}>
                                Ваши ходы:{
                                playGame.data.moves.map((row) => (
                                    <div>{row},</div>
                                ))}
                            </Grid>)
                            : (<Grid item xs={12} sm={12} md={12} lg={12}></Grid>)}
                        {playGame?.data?.result_move ?
                            (<Grid item xs={12} sm={12} md={12} lg={12}>
                                Результат:{playGame.data.result_move} {win}
                            </Grid>)
                            : (<Grid item xs={12} sm={12} md={12} lg={12}></Grid>)}

                    </Grid>

                </div>
            </div>
        </Page>
    );
}