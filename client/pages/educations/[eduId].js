import React, {useEffect} from 'react';
import Page from '../../layout/Page/Page';
import {useDispatch, useSelector} from 'react-redux';
import s from "../../styles/pages/profile.module.scss";
import {useParams} from "react-router-dom";
import {getEducation, getEducationBlocks} from "../../store/slices/educationSlice";


import PropTypes from 'prop-types';
import Tabs from '@mui/material/Tabs';
import Tab from '@mui/material/Tab';
import Box from "@mui/material/Box";
import {Typography} from "@material-ui/core";
import Markdown from "../../components/Markdown/Markdown";
import {BottomNavigation, BottomNavigationAction, Button, CardMedia, Chip, Rating} from "@mui/material";
import Paper from "@mui/material/Paper";

import TextSnippetIcon from '@mui/icons-material/TextSnippet';
import TaskIcon from '@mui/icons-material/Task';
import CheckCircleIcon from "@mui/icons-material/CheckCircle";
import CheckCircleOutlineIcon from "@mui/icons-material/CheckCircleOutline";
import router from '../../polyfills/router';

function TabPanel(props) {
    const {children, value, index, ...other} = props;

    return (
        <div
            role="tabpanel"
            hidden={value !== index}
            id={`vertical-tabpanel-${index}`}
            aria-labelledby={`vertical-tab-${index}`}
            {...other}
        >
            {value === index && (
                <Box sx={{p: 3}}>
                    <Typography>{children}</Typography>
                </Box>
            )}
        </div>
    );
}

TabPanel.propTypes = {
    children: PropTypes.node,
    index: PropTypes.number.isRequired,
    value: PropTypes.number.isRequired,
};

function a11yProps(index) {
    return {
        id: `vertical-tab-${index}`,
        'aria-controls': `vertical-tabpanel-${index}`,
    };
}


export default function education() {
    const params = useParams();
    const {eduId} = params;
    const dispatch = useDispatch();
    const education = useSelector(state => state.education.edu)
    const blocks = useSelector(state => state.education.blocks)

    // Получение объекта достижения и его блоков
    useEffect(() => {
        eduId
        && dispatch(getEducation(eduId))
        && dispatch(getEducationBlocks(eduId))
    }, [eduId]);

    // Если обучение не найдено, выкинуть 404
    useEffect(() => {
        if (education?.error?.status) {
            router.push('/404');
        }
    }, [education]);

    // console.log(education)
    // console.log(blocks)

    const [blockNumber, setBlockNumber] = React.useState(0);
    const [part, setPart] = React.useState(0);
    const [start, setStart] = React.useState(false);

    const handleChangeBlock = (event, newBlockNumber) => {
        setPart(0);
        setBlockNumber(newBlockNumber);
    };

    return (
        <Page pageTitle={'Обучение'}>
            <div className={s.backgroundStyle}>
                <div className={s.ctn}>
                    {!start ?
                        // Информация об обучении
                        <Box
                            sx={{
                                bgcolor: 'background.paper',
                            }}
                            p={5}
                        >
                            <Typography variant="h6" gutterBottom>
                                {education?.data?.name}
                            </Typography>
                            <Markdown
                                value={education?.data?.description.trim()}
                            />

                            <Chip
                                key={education?.data?.topic?.id}
                                label={education?.data?.topic?.name}
                                style={{marginTop: "10px"}}
                            />
                            <div style={{marginTop: 10, color: "dimgray"}}>
                                <Typography component="legend">Прогресс</Typography>
                                <Rating
                                    name="disabled"
                                    value={education?.data?.progress?.passed}
                                    max={education?.data?.progress?.total}
                                    icon={<CheckCircleIcon/>}
                                    emptyIcon={<CheckCircleOutlineIcon/>}
                                    disabled
                                />
                            </div>

                            <Button
                                variant="contained"
                                onClick={() => {
                                    setStart(true);
                                }}
                            >
                                Начать обучение
                            </Button>
                        </Box>
                        :
                        // Обучающие блоки
                        <Box
                            sx={{
                                flexGrow: 1,
                                bgcolor: 'background.paper',
                                display: 'flex'
                            }}
                        >
                            <Tabs
                                orientation="vertical"
                                variant="scrollable"
                                value={blockNumber}
                                onChange={handleChangeBlock}
                                aria-label="Vertical tabs example"
                                sx={{
                                    borderRight: 1,
                                    borderColor: 'divider',
                                    minWidth: 'fit-content'
                                }}
                            >
                                {
                                    blocks?.data ?
                                        blocks?.data.map(block => {
                                            return (
                                                <Tab
                                                    icon={block?.success ? <CheckCircleIcon/> : <CheckCircleOutlineIcon/> }
                                                    iconPosition="end"
                                                    label={"Блок " + block?.education_tasks?.block_number}
                                                    {...a11yProps(block?.education_tasks?.block_number)}
                                                />
                                            )
                                        })
                                        : "Loading..."
                                }
                            </Tabs>
                            {
                                blocks?.data ?
                                    blocks?.data.map(block => {
                                        return (
                                            <TabPanel
                                                value={blockNumber}
                                                index={block?.education_tasks?.block_number - 1}
                                            >
                                                <Markdown
                                                    value={
                                                        part === 0
                                                            ? block?.education_tasks?.theory_text.trim()
                                                            : block?.education_tasks?.task?.description.trim()
                                                    }
                                                />
                                                {/*Кнопки навигации Теории и Практики*/}
                                                <Paper elevation={2} sx={{width: 'fit-content', marginTop: 2}}>
                                                    <BottomNavigation
                                                        showLabels
                                                        value={part}
                                                        onChange={(event, newValue) => {
                                                            setPart(newValue);
                                                        }}
                                                    >
                                                        <BottomNavigationAction label="Теория"
                                                                                icon={<TextSnippetIcon/>}/>
                                                        <BottomNavigationAction label="Практика" icon={<TaskIcon/>}/>
                                                    </BottomNavigation>
                                                </Paper>
                                            </TabPanel>
                                        )
                                    })
                                    : "Loading..."
                            }
                        </Box>
                    }
                </div>
            </div>
        </Page>
    );
}