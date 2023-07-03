import React, {useEffect, useState} from 'react';
import s from '../../styles/tasks/creatTasks.module.scss';
import Page from "../../layout/Page/Page";
import BoxAnimation from "../../components/BoxAnimation/BoxAnimation";
import {useDispatch, useSelector} from 'react-redux';
import {Grid} from "@material-ui/core";
import {getTopicsInfo} from "../../store/slices/topicSlice";
import TextField from '@mui/material/TextField';
import MenuItem from '@mui/material/MenuItem';
import Box from '@mui/material/Box';
import {Controller, useForm} from "react-hook-form";
import Input from "../../components/Input/Input";
import {getToken} from "../../store/slices/authSlice";
import Spinner from "../../components/Spinner/Spinner";
import {Button} from "@mui/material";
import {createTask} from "../../store/slices/creatTaskSlice";
import plus from "../../public/svg/plus.svg";
import minus from "../../public/svg/minus.svg";
import plus1 from "../../public/svg/plus1.svg";
import minus1 from "../../public/svg/minus1.svg";
import CustomMDEditor from "../../components/CustomMDEditor/CustomMDEditor";

import toast, {Toaster} from 'react-hot-toast'
import SimpleToast, {notify} from "../../components/Toast/SimpleToast";

export default function tasks(userID) {
//Запрос топиков
    const topics = useSelector(state => state.topics.info);
    const dispatch = useDispatch();
    useEffect(() => {
        dispatch(getTopicsInfo());
    }, []);

    const [filters, setFilters] = useState({
        topics: []
    });

    function filtering(topics) {
        let result = topics;
        let filteredItems = [];

        if (filters.topics.length !== 0) {
            for (let i = 0; i < filters.topics.length; i++) {
                filteredItems = filteredItems.concat(
                    result.filter(topics =>
                        topics?.data?.id === filters.topics[i].id
                    )
                );
            }

            result = filteredItems;
        }

        return result;
    }

    const [topic, setTopics] = useState("");

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
    const [flag, setFlagMatrix] = useState("");

    const [rows, setRows] = useState(2); // начальное количество строк
    const [cols, setCols] = useState(2); // начальное количество столбцов
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
                <Grid item xs={4} sm={4} md={3} lg={2}>
                    <TextField
                        value={value}
                        id="col"
                        key={col}
                        type="text"
                        className={s.matrixInput}
                        onChange={(event) => updateMatrixValue(row, col, event.target.value)}
                    />
                </Grid>
            ));
        }

        console.log(matrix);

        // функция для отображения строк матрицы
        function renderRows() {
            return matrix.map((row, index) => (
                <Grid item container spacing={2} xs={12} sm={12} md={12} lg={12} key={index}>
                    Строка {index + 1}
                    <div className={s.propsRow}>
                        <Grid container spacing={2} item xs={12} sm={12} md={12} lg={12}>
                            {renderCells(index)}
                        </Grid>
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
                        )
                    }} dangerouslySetInnerHTML={{__html: minus1}} className={s.propsButton}/>

                    {rows}
                    {/*<TextField
                        className={s.propsText}
                        variant="standard"
                        type="number"
                        value={rows}
                        onChange={(event) => resizeMatrix(parseInt(event.target.value), cols)}
                    />*/}
                    <button onClick={() => {
                        resizeMatrix(parseInt(
                                (rows === 20) ?
                                    rows
                                    : (rows + 1)
                            ), cols
                        )
                    }} dangerouslySetInnerHTML={{__html: plus1}} className={s.propsButton}/>
                </Grid>
                <Grid item container xs={12} sm={12} md={6} lg={6} className={s.propsBack}>
                    Кол. столбцов:
                    <button onClick={() => {
                        resizeMatrix(rows, parseInt(
                            (cols === 2) ?
                                cols
                                : cols - 1
                        ))
                    }} dangerouslySetInnerHTML={{__html: minus1}} className={s.propsButton}/>
                    {cols}
                    <button onClick={() => {
                        resizeMatrix(rows, parseInt(
                            (cols === 20) ?
                                cols
                                : cols + 1
                        ))
                    }} dangerouslySetInnerHTML={{__html: plus1}} className={s.propsButton}/>


                </Grid>
                <Grid container item spacing={0} xs={12} sm={12} md={12} lg={12} style={{justifyContent: ' center'}}>
                    <div className={s.matrixBack}>
                        {renderRows()}
                    </div>
                </Grid>
            </Grid>
        );
    }


//для запроса
    const {handleSubmit, control, formState: {errors}} = useForm({
        mode: 'onBlur',
        defaultValues: {
            name: '',
            description: '',
            matrix: matrix,
            flag_matrix: '',
            topic_id: 101,
        }
    });

    const onSubmit = (data) => {
        console.log(data)
        dispatch(createTask(data));
    }

    return (
        <Page pageTitle={'Конструктор заданий'}>
            <div className={s.backgroundStyle}>
                <div className={s.ctn}>
                    <form onSubmit={handleSubmit(onSubmit)}>
                        <Grid container spacing={0} className={s.background}>
                            <Grid item xs={12} sm={12} md={12} lg={12} className={s.title}>
                                Новое задание
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
                                            defaultValue=""
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
                                            label="Тип марицы"
                                            defaultValue={flag}
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
                                            value={topic}
                                        >
                                            {
                                                topics?.data ?
                                                    filtering(topics?.data).map((topic) => (
                                                            <MenuItem key={topic.id} value={topic.id}
                                                                      onClick={() => setTopics(topic.id)}>
                                                                {topic.name}
                                                            </MenuItem>
                                                        )
                                                    )
                                                    : "Loading..."
                                            }
                                        </TextField>
                                    )}/>
                                {errors.topic_id &&
                                    <span style={{
                                        color: 'var(--main-brand-color)'
                                    }}>Обязательное поле</span>}
                            </Grid>
                            <Grid item xs={12} sm={12} md={12} lg={12}
                                  className={s.name}
                                  data-color-mode="light"
                            >
                                <p>Описание</p>
                                <Controller
                                    name="description"
                                    control={control}
                                    rules={{required: true}}
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

                            <Grid item xs={12} sm={12} md={12} lg={12} className={s.name} style={{
                                color: "white",
                                fontSize: '28px'
                            }}>
                                Создание матрицы
                            </Grid>

                            <Matrix/>
                            <Button type={'submit'} variant="contained">Создать</Button>
                        </Grid>
                    </form>

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