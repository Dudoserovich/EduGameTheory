import React, {useEffect, useState} from "react";
import Page from "../../layout/Page/Page";
import s from "../../styles/pages/profile.module.scss";
import {useDispatch, useSelector} from "react-redux";
import {Button, Card, CardActions, CardContent, CardMedia, Chip, TextField} from "@mui/material";
import {Typography} from "@material-ui/core";
import {getTerms} from "../../store/slices/termSlice";
import CustomSelect from "../../components/CustomSelect/CustomSelect";
import {getTopics} from "../../store/slices/topicSlice";
import {CardSkeleton} from "../../components/Skeletons/CardSkeleton";

export default function term() {
    const dispatch = useDispatch();
    const terms = useSelector(state => state.term.info);
    const topics = useSelector(state => state.topic.info);
    const [filters, setFilters] = useState({
        topics: []
    });

    useEffect(() => {
        dispatch(getTerms());
        dispatch(getTopics());
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

    return (
        <Page pageTitle={'Список литературы'}>
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
                            terms?.data ?
                                filtering(terms?.data).map(term => {
                                    return (
                                        <Card key={term.id}
                                              sx={{
                                                  marginBottom: "10px",
                                                  marginRight: "20px",
                                                  minWidth: "-webkit-fill-available"
                                              }}
                                        >
                                            <CardContent>
                                                <Typography gutterBottom variant="h5" component="div">
                                                    {term.name}
                                                </Typography>
                                                <Typography variant="body2"  style={{color: "dimgray"}}>
                                                    {term.description}
                                                </Typography>
                                                <Chip
                                                    key={term?.topic?.id}
                                                    label={term?.topic?.name}
                                                    style={{marginTop: "10px"}}
                                                />
                                            </CardContent>
                                        </Card>
                                    );
                                })
                                :
                                <>
                                    <CardSkeleton/>
                                    <CardSkeleton/>
                                    <CardSkeleton/>
                                    <CardSkeleton/>
                                </>
                        }
                    </div>
                </div>
            </div>
        </Page>
    );
}