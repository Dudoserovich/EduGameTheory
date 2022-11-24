import React from "react";
import s from "./Spinner.module.scss"

export default function Spinner() {
 
    return(
        <div className={s.spinner}>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
        </div>
    );
}