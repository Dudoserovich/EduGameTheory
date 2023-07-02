import React, {useEffect, useState} from "react";
import Page from "../../layout/Page/Page";
import s from "../../styles/pages/profile.module.scss";
import {useDispatch, useSelector} from "react-redux";
import {getLiteratures} from "../../store/slices/literatureSlice";
import {Button, Card, CardActions, CardContent, CardMedia, Chip, Rating} from "@mui/material";
import {Typography} from "@material-ui/core";
import CustomSelect from "../../components/CustomSelect/CustomSelect";
import {getTopicsInfo} from "../../store/slices/topicSlice";
import {LiteratureCardSkeleton} from "../../components/Skeletons/CardSkeleton";
import {getEducations} from "../../store/slices/educationSlice";

import CheckCircleIcon from '@mui/icons-material/CheckCircle';
import CheckCircleOutlineIcon from '@mui/icons-material/CheckCircleOutline';
import Markdown from "../../components/Markdown/Markdown";

export default function educations() {
    const dispatch = useDispatch();
    const educations = useSelector(state => state.education.info);
    const topics = useSelector(state => state.topics.info);
    const [filters, setFilters] = useState({
        topics: []
    });

    useEffect(() => {
        dispatch(getEducations());
        dispatch(getTopicsInfo());
    }, [])

    function onChangeHandler(options) {
        setFilters({...filters, ...options})
    }

    function filtering(items) {
        let result = items;
        let filteredItems = [];

        if (filters.topics.length !== 0) {
            for (let i = 0; i < filters.topics.length; i++) {
                filteredItems = filteredItems.concat(
                    result.filter(item =>
                        item?.topic?.id === filters.topics[i].id
                    )
                );
            }

            result = filteredItems;
        }

        return result;
    }

    function truncateString(str, maxLength = 200) {
        if (str.length <= maxLength) {
            return str;
        }

        // Обрезаем строку до maxLength символов
        let truncatedStr = str.substr(0, maxLength);

        // Находим последний пробел в обрезанной строке
        let lastSpaceIndex = truncatedStr.lastIndexOf(' ');

        // Если найден пробел, обрезаем строку до этого пробела
        if (lastSpaceIndex !== -1) {
            truncatedStr = truncatedStr.substr(0, lastSpaceIndex);
        }

        return truncatedStr;
    }

    return (
        <Page pageTitle={'Обучения'}>
            <div className={s.backgroundStyle}>
                <div className={s.ctn}>
                    <div style={{
                        width: "100%",
                        display: "flex",
                        justifyContent: "flex-start",
                        alignItems: "flex-start",
                        marginBottom: "12px"
                    }}>
                        <CustomSelect
                            isMulti
                            instanceId={'topic-select'}
                            className={s.filter}
                            placeholder={'Тип'}
                            closeMenuOnSelect={false}
                            isClearable={true}
                            isSearchable={false}
                            isLoading={topics.isLoading}
                            loadingMessage={() => 'Загрузка...'}
                            noOptionsMessage={() => {
                                return (topics.error ? 'Ошибка сервера' : 'Ничего нет :(');
                            }}
                            options={topics.data}
                            getOptionLabel={option => option.name}
                            getOptionValue={option => option.id}
                            onChange={options => onChangeHandler({topics: options})}
                        />
                    </div>
                    <div style={{
                        gridTemplateColumns: "repeat(auto-fill, minmax(370px, 2fr))",
                        display: "grid",
                        placeItems: "center"
                    }}>
                        {
                            educations?.data ?
                                filtering(educations?.data)?.map(education => {
                                    return (
                                        <Card key={education.id}
                                              sx={{
                                                  marginBottom: "10px",
                                                  marginRight: "20px",
                                                  minWidth: "-webkit-fill-available",
                                                  // minHeight: 200
                                              }}
                                        >
                                            <CardMedia
                                                sx={{height: 350}}
                                                component="img"
                                                image="https://cataas.com/cat?type=sm"
                                            />
                                            <CardContent>
                                                <Typography gutterBottom variant="h5" component="div">
                                                    {education.name}
                                                </Typography>
                                                <Markdown
                                                    value={truncateString(education?.description) + '...'}
                                                />
                                                <Chip
                                                    key={education?.topic?.id}
                                                    label={education?.topic?.name}
                                                    style={{marginTop: "10px"}}
                                                />
                                                <div style={{marginTop: 10, color: "dimgray"}}>
                                                    <Typography component="legend">Прогресс</Typography>
                                                    <Rating
                                                        name="disabled"
                                                        value={education?.progress?.passed}
                                                        max={education?.progress?.total}
                                                        icon={<CheckCircleIcon/>}
                                                        emptyIcon={<CheckCircleOutlineIcon/>}
                                                        disabled
                                                    />
                                                </div>
                                            </CardContent>
                                            <CardActions>
                                                <Button
                                                    href={`/educations/${education.id}`}
                                                    size="small"
                                                >Перейти к обучению
                                                </Button>
                                            </CardActions>
                                        </Card>
                                    );
                                })
                                :
                                <>
                                    <LiteratureCardSkeleton/>
                                    <LiteratureCardSkeleton/>
                                    <LiteratureCardSkeleton/>
                                    <LiteratureCardSkeleton/>
                                </>
                        }
                    </div>
                </div>
            </div>
        </Page>
    );
}