import React, {useEffect} from "react";
import Page from "../../layout/Page/Page";
import s from "../../styles/pages/profile.module.scss";
import {useDispatch, useSelector} from "react-redux";
import ColumnGroupingTable from "../../components/Table";
import {Typography} from "@material-ui/core";
import {getUsers} from "../../store/slices/generalSlice";
import {getUserRole} from "../../scripts/rolesConfig";

export default function users() {
    const dispatch = useDispatch();
    const users = useSelector(state => state.general.users);

    useEffect(() => {
        dispatch(getUsers());
    }, [])

    function getLoginWithAvatar(login, avatar) {
        return (
            <div style={{
                display: "flex",
                alignItems: "center",
                justifyContent: "center",
                flexDirection: "column"
            }}>
                <img
                    style={{width: "50px", height: "50px", borderRadius: "50%", minWidth: "fit-content"}}
                    src={avatar}
                ></img>
                <Typography variant="body2" color="secondary">{login}</Typography>
            </div>
        )
    }

    return (
        <Page pageTitle={'Пользователи'}>
            <div className={s.backgroundStyle}>
                <div className={s.ctn}>
                    <div>
                        {
                            users.isLoading ?
                                "Loading..."
                                :
                            <ColumnGroupingTable
                                header={['', 'ФИО', 'Роли', 'Email']}
                                data={
                                    users.data.map(({full_name, login, roles, email, avatar}) => {
                                        return {
                                            login: getLoginWithAvatar(login, avatar),
                                            full_name,
                                            role: getUserRole(roles).label,
                                            email
                                        }
                                    })
                                }
                                buttons={true}
                            />
                        }
                    </div>
                </div>
            </div>
        </Page>
    );
}