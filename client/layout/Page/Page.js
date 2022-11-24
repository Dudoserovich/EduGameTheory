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

export default function Page({children, pageTitle = ''}) {
    const [ decodedToken, setDecodedToken ] = useState(null);
    const [ navConfig, setNavConfig ] = useState(headerConfig.user);

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

    return (
        <>
            <Head>
                <title>{pageTitle && `${pageTitle} - `}EduGameTheory</title>
                <meta name="viewport" content="width=device-width"/>
            </Head>
            <div className={s.Page}>
                <header>
                    <Header username={decodedToken?.username} navConfig={navConfig}/>
                </header>

                <main>
                    {children}
                </main>

                <footer>
                    <Footer/>
                </footer>
            </div>
        </>
    );
}