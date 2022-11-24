import React from "react";
import Auth from "../components/Auth/Auth";
import Head from "../polyfills/head";

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
