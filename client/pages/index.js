import React, {useEffect} from "react";
import Auth from "../components/Auth/Auth";
import Head from "../polyfills/head";

import Toast, {notify} from "../components/Toast/Toast";

export default function Home() {
    return (
      <>
        <Head>
            <title>О нас (EduGameTheory)</title>
            <meta
                name="viewport"
                content="width=375, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes"
            />
        </Head>
        <div>
            <Auth/>
        </div>
      </>
    )
}
