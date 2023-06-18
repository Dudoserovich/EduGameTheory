import React, {useEffect, useState} from 'react';
import s from '../../styles/tasks/tasks.module.scss';
import Page from "../../layout/Page/Page";
import BoxAnimation from "../../components/BoxAnimation/BoxAnimation";
import {useDispatch, useSelector} from 'react-redux';
import {Grid} from "@material-ui/core";
import down from '../../public/svg/down.svg'
import up from '../../public/svg/up.svg'
import edit from '../../public/svg/edit.svg'
import delet from '../../public/svg/delete1.svg'
import {getTasksInfo} from "../../store/slices/tasksSlice";
import {getUserInfo} from "../../store/slices/userSlice";
import { useNavigate} from 'react-router-dom';


export default function tasks() {

    const tasks = useSelector(state => state.tasks.info);
    const user = useSelector(state => state.user.info);

    const dispatch = useDispatch();

    useEffect(() => {
        dispatch(getTasksInfo());
        dispatch(getUserInfo());
    }, []);

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
    const navigate = useNavigate();
    function MyTask(props) {
        const { task } = props;
        const handleClick = () => {
        props.navigate('/tasks/task', {
                state:
                    {
                        task: task
                    }
            }
            );
        }

        return (
            <button onClick={handleClick}>Начать</button>
        );
    }

    function GoEditTask(props) {
        const { task } = props;
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
            <div className={s.more} onClick={handleClick} dangerouslySetInnerHTML={{__html: edit}}/>
        );
    }

    function ListTask({task}) {
        const [showDetails, setShowDetails] = useState(false);
        return (
            <div>
                <Grid container spacing={0} className={s.task}>
                    <Grid item xs={12} sm={3} md={3} lg={3} className={s.name}>
                        {task.name}
                    </Grid>
                    <Grid item xs={12} sm={4} md={3} lg={3} className={s.fio}>
                        Создатель<br/>
                        {(task.owner != null) ?
                            task.owner.fio
                            : "Задание кота"}
                    </Grid>
                    <Grid item xs={12} sm={2} md={2} lg={2} className={s.topic}>
                        Тип<br/>
                        {task.topic.name}
                    </Grid>
                    {(task.owner != null && user.data?.full_name) ?
                        (task.owner.id === user.data?.id) ?
                            <Grid container  item xs={4} sm={1} md={1} lg={1}>
                                <Grid item xs={6} sm={6} md={6} lg={6}>
                                    <GoEditTask task={task} navigate={navigate}/>
                                </Grid>
                                <Grid item xs={6} sm={6} md={6} lg={6}>
                                    <div className={s.more}
                                         onClick={() => setShowDetails(!showDetails)}
                                         dangerouslySetInnerHTML={{__html: delet}}/>
                                </Grid>
                            </Grid>
                            : <Grid item xs={4} sm={1} md={1} lg={1}><div></div></Grid>
                        : <Grid item xs={4} sm={1} md={1} lg={1}><div></div></Grid>
                    }
                    <Grid container spacing={0} xs={8} sm={2} md={2} lg={2} className={s.buttons}>
                        <Grid item xs={3} sm={3} md={3} lg={3}>
                            {showDetails ?
                                (
                                    <div className={s.more}
                                         onClick={() => setShowDetails(!showDetails)}
                                         dangerouslySetInnerHTML={{__html: up}}/>
                                ) :
                                (
                                    <div className={s.more} onClick={() => setShowDetails(!showDetails)}
                                         dangerouslySetInnerHTML={{__html: down}}/>
                                )
                            }
                        </Grid>
                        <Grid item xs={9} sm={9} md={9} lg={9} className={s.button}>
                            <MyTask task={task} navigate={navigate}/>
                        </Grid>
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
                                <div className={s.description}>{task.description}</div>

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
        );
    }

    return (
        <Page pageTitle={'Задания'}>
            <div className={s.backgroundStyle}>
                <div className={s.ctn}>
                    <div>
                        {
                            tasks?.data ?
                                filtering(tasks?.data).map((task) => (
                                        <ListTask key={task.id} task={task}/>
                                    )
                                )
                                : "Loading..."
                        }
                    </div>
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

            </div>
        </Page>
    );
}