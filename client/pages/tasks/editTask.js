import React, {useEffect, useState} from 'react';
import s from '../../styles/tasks/creatTasks.module.scss';
import Page from "../../layout/Page/Page";
import BoxAnimation from "../../components/BoxAnimation/BoxAnimation";
import {useDispatch, useSelector} from 'react-redux';
import {Grid, Typography} from "@material-ui/core";
import {getTopicsInfo} from "../../store/slices/topicSlice";
import TextField from '@mui/material/TextField';
import MenuItem from '@mui/material/MenuItem';
import {Controller, useForm} from "react-hook-form";
import Input from "../../components/Input/Input";
import {getToken} from "../../store/slices/authSlice";
import Spinner from "../../components/Spinner/Spinner";
import {Button, Modal} from "@mui/material";
import plus1 from "../../public/svg/plus1.svg";
import minus1 from "../../public/svg/minus1.svg";
import {useLocation} from 'react-router-dom';
import {updateTaskInfo} from "../../store/slices/tasksSlice";
import CustomMDEditor from "../../components/CustomMDEditor/CustomMDEditor";
import SimpleToast, {notify} from "../../components/Toast/SimpleToast";
import MuiCircularProgress from "../../components/Spinner/MuiCircularProgress";
import Markdown from "../../components/Markdown/Markdown";
import {checkMatrixInfo, createTask} from "../../store/slices/creatTaskSlice";


export default function tasks() {
    const {state} = useLocation();
    const task = state.task;
//Запрос топиков
    const topics = useSelector(state => state.topics.info);
    const dispatch = useDispatch();
    useEffect(() => {
        dispatch(getTopicsInfo());
    }, []);

    const flagMatrix = [
        {
            id: '0',
            name: 'платёжная матрица',
        },
        {
            id: '1',
            name: 'матрица последствий',
        },
    ];

    const [rows, setRows] = useState(2); // начальное количество строк
    const [cols, setCols] = useState(2); // начальное количество столбцов

    const [fields, setFields] = useState(Array.from({length: rows}, () => Array.from(1, () => '')));
    const [fields2, setFields2] = useState(Array.from({length: cols}, () => Array.from(1, () => '')));
    const [matrix, setMatrix] = useState(
        Array.from({length: rows}, () => Array.from({length: cols}, () => 0))
    );

    function Matrix() { // начальное значение матрицы

        // функция для изменения размера матрицы
        function resizeMatrix(newRows, newCols) {
            setRows(newRows);
            setCols(newCols);
            setMatrix(
                Array.from({length: newRows}, () => Array.from({length: newCols}, () => ""))
            );
        }

        // функция для обновления значения в ячейке матрицы
        function updateMatrixValue(row, col, value) {
            const newMatrix = [...matrix];
            newMatrix[row][col] = value;
            setMatrix(newMatrix);
        }

        // функция для отображения ячеек матрицы
        function renderCells(row) {
            return matrix[row].map((value, col) => (
                <TextField
                    defaultValue={value}
                    id="col"
                    key={col}
                    type="text"
                    className={s.matrixInput}
                    onBlur={(event) => updateMatrixValue(row, col, parseFloat(event.target.value))}
                />
            ));
        }


        // функция для отображения строк матрицы
        function renderRows() {
            return matrix.map((row, index) => (
                <Grid item container spacing={2} xs={12} sm={12} md={12} lg={12} key={index}>
                    Строка {index + 1}
                    <div className={s.propsRow}>
                        {renderCells(index)}
                    </div>
                </Grid>
            ));
        }

        return (
            <Grid container spacing={0} style={{height: `100%`}}>
                <Grid item container spacing={2} xs={12} sm={12} md={6} lg={6} className={s.propsBack}>
                    Кол. строк:
                    <button onClick={() => {
                        resizeMatrix(parseInt(
                                (rows === 2) ?
                                    rows
                                    : (rows - 1)
                            ), cols
                        );
                        (rows !== 2) ?
                            handleRemoveField()
                            : null
                    }} dangerouslySetInnerHTML={{__html: minus1}} className={s.propsButton}/>

                    {rows}
                    <button onClick={() => {
                        resizeMatrix(parseInt(
                                (rows === 12) ?
                                    rows
                                    : (rows + 1)
                            ), cols
                        );
                        (rows !== 12) ?
                            handleAddField()
                            : null

                    }} dangerouslySetInnerHTML={{__html: plus1}} className={s.propsButton}/>
                </Grid>
                <Grid item container xs={12} sm={12} md={6} lg={6} className={s.propsBack}>
                    Кол. столбцов:
                    <button onClick={() => {
                        resizeMatrix(rows, parseInt(
                            (cols === 2) ?
                                cols
                                : cols - 1
                        ));
                        (cols !== 2) ?
                            handleRemoveField2()
                            : null
                    }} dangerouslySetInnerHTML={{__html: minus1}} className={s.propsButton}/>
                    {cols}
                    <button onClick={() => {
                        resizeMatrix(rows, parseInt(
                            (cols === 12) ?
                                cols
                                : cols + 1
                        ));
                        (cols !== 12) ?
                            handleAddField2()
                            : null
                    }} dangerouslySetInnerHTML={{__html: plus1}} className={s.propsButton}/>


                </Grid>
                <YourComponent/>
                <YourComponent2/>

                <div className={s.matrixBack}>
                    {renderRows()}
                </div>
            </Grid>
        );
    }

//для запроса
    const {handleSubmit, control, formState: {errors}} = useForm({
        mode: 'onBlur',
        defaultValues: {
            name: task.name,
            description: task.description,
            matrix: matrix,
            flag_matrix: task.flag_matrix,
            topic_id: task?.topic?.id,
            name_first_player: task.name_first_player,
            name_second_player: task.name_second_player,
        }
    });

    const [newTaskData, setNewTask] = React.useState({});
    const onSubmit = (data) => {

        console.log(data);
        handleOpen()
        setNewTask(data)
        dispatch(
            checkMatrixInfo({
                matrix: data.matrix,
                flag_matrix: data.flag_matrix
            })
        );
    }

    const matrixInfo = useSelector(state => state.newTask.matrixInfo);
    // console.log(matrixInfo)

    const [open, setOpen] = React.useState(false);
    const handleOpen = () => setOpen(true);
    const handleClose = () => setOpen(false);
    const handleCloseWithUpdate = () => {
        setOpen(false);

        const newTask = {
            name: newTaskData.name,
            description: newTaskData.description,
            matrix: matrix,
            flag_matrix: newTaskData.flag_matrix,
            topic_id: newTaskData.topic_id,
            name_first_player: task.name_first_player,
            name_second_player: task.name_second_player,
            name_first_strategies: fields,
            name_second_strategies: fields2,
        }

        dispatch(updateTaskInfo({id: task.id, IData: newTask}));
    };

    const styleModal = {
        position: 'absolute',
        top: '50%',
        left: '50%',
        transform: 'translate(-50%, -50%)',
        width: 400,
        bgcolor: 'background.paper',
        border: '2px solid #000',
        boxShadow: 24,
        p: 4,
    };

    }
    const handleAddField = () => {
        setFields([...fields, '']);
    };
    const handleRemoveField = () => {
        const updatedFields = [...fields];
        updatedFields.splice(fields.length - 1, 1);
        setFields(updatedFields);
    };

    function YourComponent() {
        const handleChange = (value, index) => {
            const updatedFields = [...fields];
            updatedFields[index] = value;
            setFields(updatedFields);
        };

        const handleSubmit = (e) => {
            e.preventDefault();
            console.log(fields);
        };

        return (
            <Grid container item xs={12} sm={12} md={12} lg={12} className={s.name} style={{wight: '100%',}}>
                <Grid item xs={12} sm={12} md={12} lg={12}
                      style={{marginBottom: `10px`, color: ' white', fontSize: '20px'}}>
                    Стратегии 1-го игрока (строки):
                </Grid>
                <form onSubmit={handleSubmit} style={{width: '100%'}}>
                    <Grid container item xs={12} sm={12} md={12} lg={12} className={s.name} style={{wight: '100%',}}>
                        {fields.map((value, index) => (
                            <Grid key={index} item xs={12} sm={6} md={4} lg={3} style={{paddingBottom: '10px', paddingRight: "10px"}}>
                                <Controller
                                    name={`name_player_${index}`}
                                    control={control}
                                    rules={{required: true}}
                                    render={({field}) => (
                                        <TextField
                                            {...field}
                                            defaultValue={value}
                                            required
                                            type="text"
                                            color="info"
                                            style={{
                                                borderRadius: '4px',
                                                backgroundColor: 'white',
                                                width: '100%',
                                            }}
                                            id={`name_player_${index}`}
                                            label={`Название ${index + 1}-ой стратегии`}
                                            value={field[index]}
                                            onBlur={(e) => handleChange(e.target.value, index)}
                                        />
                                    )}
                                />
                                {errors[`name_player_${index}`] && (
                                    <span style={{color: 'var(--main-brand-color)'}}>Обязательное поле</span>
                                )}
                            </Grid>
                        ))}
                    </Grid>
                </form>
            </Grid>
        );
    };
    const handleAddField2 = () => {
        setFields2([...fields2, '']);
    };
    const handleRemoveField2 = () => {
        const updatedFields = [...fields2];
        updatedFields.splice(fields2.length - 1, 1);
        setFields2(updatedFields);
    };

    function YourComponent2() {
        const handleChange = (value, index) => {
            const updatedFields = [...fields2];
            updatedFields[index] = value;
            setFields2(updatedFields);
        };

        const handleSubmit = (e) => {
            e.preventDefault();
            console.log(fields2);
        };

        return (
            <Grid container item xs={12} sm={12} md={12} lg={12} className={s.name}>
                <Grid item xs={12} sm={12} md={12} lg={12}
                      style={{marginBottom: `10px`, color: ' white', fontSize: '20px'}}>
                    Стратегии 2-го игрока (строки):
                </Grid>
                <form onSubmit={handleSubmit} style={{
                    width: '100%'
                }}>
                    <Grid container item xs={12} sm={12} md={12} lg={12} className={s.name} style={{wight: '100%',}}>
                        {fields2.map((value, index) => (
                            <Grid key={index} item xs={12} sm={6} md={4} lg={3} style={{paddingBottom: '10px', paddingRight: "10px"}}>
                                <Controller
                                    name={`name_player_${index}`}
                                    control={control}
                                    rules={{required: true}}
                                    render={({field}) => (
                                        <TextField
                                            {...field}
                                            defaultValue={value}
                                            required
                                            type="text"
                                            color="info"
                                            style={{
                                                borderRadius: '4px',
                                                backgroundColor: 'white',
                                                width: '100%',
                                            }}
                                            id={`name_player_${index}`}
                                            label={`Название ${index + 1}-ой стратегии`}
                                            value={field[index]}
                                            onBlur={(e) => handleChange(e.target.value, index)}
                                        />
                                    )}
                                />
                                {errors[`name_player_${index}`] && (
                                    <span style={{color: 'var(--main-brand-color)'}}>Обязательное поле</span>
                                )}
                            </Grid>
                        ))}
                    </Grid>
                </form>
            </Grid>
        );
    };
    return (
        <Page pageTitle={'Изменение задания'}>
            <div className={s.backgroundStyle}>
                <div className={s.ctn}>
                    <form onSubmit={handleSubmit(onSubmit)}>
                        <Grid container spacing={0} className={s.background}>
                            <Grid item xs={12} sm={12} md={12} lg={12} className={s.title}>
                                Редактор задания
                            </Grid>
                            <Grid item xs={12} sm={12} md={4} lg={4} className={s.name}>
                                <Controller
                                    name="name"
                                    control={control}
                                    rules={{required: true}}
                                    render={({field}) => (
                                        <TextField
                                            {...field}
                                            type={"text"}
                                            color="info"
                                            style={{
                                                borderRadius: '4px',
                                                backgroundColor: "white",
                                                width: '100%',
                                            }}
                                            id="outlined-helperText"
                                            label="Название"

                                        />
                                    )}/>
                                {errors.name &&
                                    <span style={{
                                        color: 'var(--main-brand-color)'
                                    }}>Обязательное поле</span>}
                            </Grid>
                            <Grid item xs={12} sm={12} md={3} lg={3} className={s.name}>
                                <Controller
                                    name="flag_matrix"
                                    control={control}
                                    rules={{required: true}}
                                    render={({field}) => (
                                        <TextField
                                            {...field}
                                            type={"text"}
                                            style={{
                                                borderRadius: '4px',
                                                backgroundColor: "white",
                                                width: '100%'
                                            }}
                                            id="outlined-select-currency"
                                            select
                                            label="Тип матрицы"
                                            // defaultValue={flag}
                                        >
                                            {
                                                flagMatrix ?
                                                    flagMatrix?.map((flag) => (
                                                            <MenuItem key={flag.id} value={flag.name}>
                                                                {flag.name}
                                                            </MenuItem>
                                                        )
                                                    )
                                                    : "Loading..."
                                            }
                                        </TextField>
                                    )}/>
                                {errors.flag_matrix &&
                                    <span style={{
                                        color: 'var(--main-brand-color)'
                                    }}>Обязательное поле</span>}
                            </Grid>
                            <Grid item xs={12} sm={12} md={3} lg={3} className={s.name}>
                                <Controller
                                    name="topic_id"
                                    control={control}
                                    rules={{required: true}}
                                    render={({field}) => (
                                        <TextField
                                            {...field}
                                            type={"number"}
                                            style={{
                                                borderRadius: '4px',
                                                backgroundColor: "white",
                                                width: '100%'
                                            }}
                                            id="outlined-select-currency"
                                            select
                                            label="Тип задания"
                                            value={!topics.isLoading ? field.value : ""}
                                        >
                                            {
                                                topics.isLoading ?
                                                    "Loading..."
                                                    :
                                                    topics?.data?.map((topic) => (
                                                        <MenuItem key={topic?.id} value={topic?.id}>
                                                            {topic?.name}
                                                        </MenuItem>
                                                    ))
                                            }
                                        </TextField>
                                    )}/>
                                {errors.topic_id &&
                                    <span style={{
                                        color: 'var(--main-brand-color)'
                                    }}>Обязательное поле</span>}
                            </Grid>
                            <Grid item xs={12} sm={12} md={12} lg={12}
                                  data-color-mode="light"
                                  className={s.name}
                            >
                                <Typography
                                    style={{color: "white"}}
                                    variant="h6" component="h2"
                                >
                                    Описание
                                </Typography>
                                <Controller
                                    name="description"
                                    control={control}
                                    rules={{required: false}}
                                    render={(
                                        {
                                            field: {onChange, onBlur, value, name, ref},
                                            fieldState: {invalid, isTouched, isDirty, error},
                                            formState,
                                        }) => (
                                        <CustomMDEditor
                                            value={value}
                                            onChange={onChange}
                                        />
                                    )}/>
                            </Grid>
                            <Grid item xs={12} sm={6} md={4} lg={4} className={s.name}>
                                <Controller
                                    name="name_first_player"
                                    control={control}
                                    rules={{required: true}}
                                    render={({field}) => (
                                        <TextField
                                            {...field}
                                            required
                                            type={"text"}
                                            color="info"
                                            style={{
                                                borderRadius: '4px',
                                                backgroundColor: "white",
                                                width: '100%',
                                            }}
                                            id="outlined-helperText"
                                            label="Имя 1-го игрока"
                                            defaultValue=""
                                        />
                                    )}/>
                                {errors.name_first_player &&
                                    <span style={{
                                        color: 'var(--main-brand-color)'
                                    }}>Обязательное поле</span>}
                            </Grid>
                            <Grid item xs={12} sm={6} md={4} lg={4} className={s.name}>
                                <Controller
                                    name="name_second_player"
                                    control={control}
                                    rules={{required: true}}
                                    render={({field}) => (
                                        <TextField
                                            {...field}
                                            required
                                            type={"text"}
                                            color="info"
                                            style={{
                                                borderRadius: '4px',
                                                backgroundColor: "white",
                                                width: '100%',
                                            }}
                                            id="outlined-helperText"
                                            label="Имя 2-го игрока"
                                            defaultValue=""
                                        />
                                    )}/>
                                {errors.name_first_player &&
                                    <span style={{
                                        color: 'var(--main-brand-color)'
                                    }}>Обязательное поле</span>}
                            </Grid>
                            <Grid item xs={12} sm={12} md={12} lg={12} className={s.name} style={{
                                color: "white",
                                fontSize: '28px'
                            }}>
                                Создание матрицы
                            </Grid>

                            <Matrix/>
                            <Button type={'submit'} variant="contained">Изменить</Button>
                        </Grid>
                    </form>

                    <div>
                        <Modal
                            open={open}
                            onClose={handleClose}
                            aria-labelledby="modal-modal-title"
                            aria-describedby="modal-modal-description"
                        >
                            <Box sx={styleModal}>
                                <Typography style={{color: "black"}} id="modal-modal-title" variant="h6" component="h2">
                                    Подтвердите изменение
                                </Typography>
                                {matrixInfo.isLoading ?
                                    <MuiCircularProgress/>
                                    :
                                    <>
                                        <Typography id="modal-modal-description" sx={{mt: 2}}>
                                            <Markdown value={matrixInfo?.data?.message}/>
                                            <p><br/>Вы уверены, что хотите изменить задание?</p>
                                        </Typography>
                                        <div style={{
                                            paddingTop: 10,
                                            display: "flex",
                                            justifyContent: "center"
                                        }}>
                                            <Button onClick={handleCloseWithUpdate}>Да</Button>
                                            <Button onClick={handleClose}>Нет</Button>
                                        </div>
                                    </>
                                }
                            </Box>
                        </Modal>
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