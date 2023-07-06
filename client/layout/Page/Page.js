import React, { useEffect, useState } from "react";
import s from './Page.module.scss';
import Head from "../../polyfills/head";
import Header from "../../components/Header/Header";
import Footer from "../../components/Footer/Footer";
import jwtDecode from "jwt-decode";
import { getJWT } from "../../scripts/jwtService";
import router from "../../polyfills/router";
import { getHeaderConfigByRole, headerConfig } from "../../scripts/headerConfig";
import { getUserRole } from "../../scripts/rolesConfig";
import BoxAnimation from "../../components/BoxAnimation/BoxAnimation";
import sp from '../../styles/pages/profile.module.scss';
import ContactPage from "../../components/IndexPage/ContactsPage";
import {useSelector} from "react-redux";

export default function Page({children, pageTitle = ''}) {
    const [ decodedToken, setDecodedToken ] = useState(null);
    const [ navConfig, setNavConfig ] = useState(headerConfig.user);
    const user = useSelector(state => state.user.info);

    useEffect(() => {
        const token = getJWT()

        try {
            let decodedToken = jwtDecode(token)
            
            let userRole = getUserRole(decodedToken?.roles);
            let headerConfig = getHeaderConfigByRole(userRole);
            setNavConfig(headerConfig);

            setDecodedToken(decodedToken);
        } catch(e) {
            console.error('Invalid JWT Token');
            router.push('/');
        }
    }, []);

    // console.log(user?.data?.login)

    return (
        <>
            <Head>
                <title>{pageTitle && `${pageTitle} - `}EduGameTheory</title>
                <meta name="viewport" content="width=device-width"/>
                {/*<link rel="icon" type="image/png" href="" sizes="16x16"/>*/}
            </Head>
            <div className={s.Page}>
                <header>
                    <Header username={user?.data?.login} navConfig={navConfig}/>
                </header>

                <main>
                    {children}
                </main>
                <ul className={sp.boxArea}>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                    <li></li>
                </ul>
                <BoxAnimation/>

                <footer>
                    <Footer/>
                </footer>
            </div>
        </>
    );
}