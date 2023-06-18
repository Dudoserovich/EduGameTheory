import React, {useEffect, useState} from "react";
import Page from "../../layout/Page/Page";
import s from "../../styles/pages/profile.module.scss";
import {useDispatch, useSelector} from "react-redux";
import {getLiteratures} from "../../store/slices/literatureSlice";
import {Button, Card, CardActions, CardContent, CardMedia, Chip} from "@mui/material";
import {Typography} from "@material-ui/core";
// import ogs from 'open-graph-scraper';
import {getLinkPreview, getPreviewFromContent} from "link-preview-js";
import CustomSelect from "../../components/CustomSelect/CustomSelect";
import {getTopics, getTopicsInfo} from "../../store/slices/topicSlice";
import {LiteratureCardSkeleton} from "../../components/Skeletons/CardSkeleton";

export default function literature() {
    // const ogs = require('open-graph-scraper');
    const dispatch = useDispatch();
    const literatures = useSelector(state => state.literature.info);
    const topics = useSelector(state => state.topics.info);
    const [filters, setFilters] = useState({
        topics: []
    });

    useEffect(() => {
        dispatch(getLiteratures());
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
                filteredItems = filteredItems.concat(result.filter(item =>
                    item.topic?.map((topic) => topic.id).includes(filters.topics[i].id))
                );
            }

            // Есть какое-то задубление, поэтому оставляем только уникальные значения
            result = new Set(filteredItems);
            result = Array.from(result);
        }

        return result;
    }

    // console.log(literatures)

    // async function ogtPreview(link) {
    //     const data = await getLinkPreview("https://cataas.com/cat");
    //     return data.favicons[0];
    // }

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
                            literatures?.data ?
                                filtering(literatures?.data).map(literature => {
                                    // ogtPreview(literature.link)
                                    // console.log(link)
                                    return (
                                        <Card key={literature.id}
                                              sx={{
                                                  marginBottom: "10px",
                                                  marginRight: "20px",
                                                  minWidth: "-webkit-fill-available"
                                              }}
                                        >
                                            <CardMedia
                                                sx={{height: 140}}
                                                component="img"
                                                image="https://cataas.com/cat?type=sm"
                                            />
                                            <CardContent>
                                                <Typography gutterBottom variant="h5" component="div">
                                                    {literature.name}
                                                </Typography>
                                                <Typography variant="body2" style={{color: "dimgray"}}>
                                                    {literature.description}
                                                </Typography>
                                                {literature?.topic?.map(topic => {
                                                    return (
                                                        <Chip
                                                            key={topic?.id}
                                                            label={topic?.name}
                                                            style={{marginRight: "10px", marginTop: "10px"}}
                                                        />
                                                    )
                                                })}
                                            </CardContent>
                                            <CardActions>
                                                <Button
                                                    target="_blank"
                                                    href={literature.link}
                                                    size="small"
                                                >Перейти на ресурс
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