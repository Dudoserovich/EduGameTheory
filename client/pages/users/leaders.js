import React, {useEffect, useState} from "react";
import Page from "../../layout/Page/Page";
import s from "../../styles/users/leaders.module.scss";
import {useDispatch, useSelector} from "react-redux";
import {Grid} from "@material-ui/core";
import Avatar from "@mui/material/Avatar";
import {getLeadersInfo} from "../../store/slices/leadersSlice";
import leaderSVG from "../../public/svg/leaders.svg";
import starSVG from "../../public/svg/star.svg";
import levelSVG from "../../public/svg/level.svg";
import {styled} from "@mui/material/styles";
import Badge from "@mui/material/Badge";

export default function leaders() {
    const dispatch = useDispatch();
    const leaders = useSelector(state => state.leaders.info);


    useEffect(() => {
        dispatch(getLeadersInfo());
    }, [])
    console.log(leaders)

    const StyledBadge = styled(Badge)(({theme}) => ({
        '& .MuiBadge-badge': {
            backgroundColor: '#44b700',
            color: '#44b700',
            boxShadow: `0 0 0 2px ${theme.palette.background.paper}`,
            '&::after': {
                position: 'absolute',
                top: 0,
                left: 0,
                width: '100%',
                height: '100%',
                borderRadius: '50%',
                animation: 'ripple 1.2s infinite ease-in-out',
                border: '1px solid currentColor',
                content: '""',
            },
        },
        '@keyframes ripple': {
            '0%': {
                transform: 'scale(.8)',
                opacity: 1,
            },
            '100%': {
                transform: 'scale(2.4)',
                opacity: 0,
            },
        },
    }));
    function ListUsers(props) {
        const {fio, id} = props;
        const [randomColor, setRandomColor] = useState('white');
        const [padding, setPadding] = useState(`10px`);

        const generateRandomColor = () => {
            if (id === 0){
                setRandomColor('#FFF496');
                setPadding('20px');
            } else if(id < 3){
                setRandomColor('#b5d2ff');
                setPadding('15px');
            } else {
                setRandomColor('white');
                setPadding('10px');
            };
        };
        useEffect(() => {
            generateRandomColor();
        }, [])
        return (
            <div>
                <Grid container spacing={0} className={s.leader}
                      style={{
                          backgroundColor: randomColor
                      }}>
                    <Grid container item xs={12} sm={6} md={6} lg={8} className={s.name}
                    style={{
                        paddingTop: padding,
                        paddingBottom: padding,
                    }}>
                        <Grid container item xs={3} sm={3} md={3} lg={2}>
                            <StyledBadge
                                overlap="circular"
                                anchorOrigin={{vertical: 'bottom', horizontal: 'right'}}
                                variant="dot"
                                className={s.userAvatar}
                            >
                                <Avatar
                                    className={s.true__icon}
                                    src={fio.user.avatar_base64}
                                />
                                {/*}*/}
                            </StyledBadge>
                        </Grid>
                        <Grid container item xs={9} sm={9} md={9} lg={10}>
                            {fio.user.fio}
                        </Grid>
                    </Grid>
                    <Grid container item xs={6} sm={3} md={2} lg={2} className={s.name}>
                        <div className={s.level} dangerouslySetInnerHTML={{__html: levelSVG}}/>
                             {fio.current_level.name}
                    </Grid>
                    <Grid container item xs={6} sm={3} md={2} lg={2} className={s.name}>
                       Очки: {fio.scores}
                        <div className={s.star} dangerouslySetInnerHTML={{__html: starSVG}}/>
                    </Grid>
                </Grid>
            </div>
        );
    }

    return (
        <Page pageTitle={'Таблица лидеров'}>
            <div className={s.backgroundStyle}>
                <div className={s.ctn}>
                    <div className={s.svg} dangerouslySetInnerHTML={{__html: leaderSVG}}/>
                    <div className={s.d14}></div>
                    <div className={s.table}>
                        {
                            leaders?.data?
                                leaders?.data?.map(( leader, id) => (
                                        <ListUsers key={leader.user.id} fio={leader} id={id}/>
                                    )
                                )
                                : "Loading..."
                        }
                    </div>
                </div>
            </div>
        </Page>
    );
}