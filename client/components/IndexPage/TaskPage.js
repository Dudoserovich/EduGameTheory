import React from "react";
import {Grid, Hidden} from "@material-ui/core";
import s from './Style/Left.module.scss';
import catSvg from './svg/task.svg';
import AOS from 'aos';
import 'aos/dist/aos.css';
import Buttom from '../Auth/ButtonAuth';

export default function ProjectPage() {
    AOS.init({
        disable: false, // accepts following values: 'phone', 'tablet', 'mobile', boolean, expression or function
        startEvent: 'DOMContentLoaded', // name of the event dispatched on the document, that AOS should initialize on
        initClassName: 'aos-init', // class applied after initialization
        animatedClassName: 'aos-animate', // class applied on animation
        useClassNames: false, // if true, will add content of `data-aos` as classes on scroll
        disableMutationObserver: false, // disables automatic mutations' detections (advanced)
        debounceDelay: 50, // the delay on debounce used while resizing window (advanced)
        throttleDelay: 99, // the delay on throttle used while scrolling the page (advanced)
        // Settings that can be overridden on per-element basis, by `data-aos-*` attributes:
        offset: 120, // offset (in px) from the original trigger point
        delay: 100, // values from 0 to 3000, with step 50ms
        duration: 1600, // values from 0 to 3000, with step 50ms
        easing: 'ease', // default easing for AOS animations
        once: false, // whether animation should happen only once - while scrolling down
        mirror: true, // whether elements should animate out while scrolling past them
        anchorPlacement: 'top-bottom',
    });

    return (
        <>
            <Grid container spacing={2} className={s.about_us}>
                <Grid item sm={12} md={8} lg={6}>
                    <h1 data-aos="fade-down"
                        data-aos-anchor-placement="bottom-bottom">ЗАДАНИЯ</h1>
                    <h4 data-aos="fade-right"
                        data-aos-anchor-placement="bottom-bottom">
                        Преподаватель может самостоятельно создавать задания!
                    </h4>
                    <h3 data-aos="fade-up"
                        data-aos-anchor-placement="bottom-bottom">
                        Есть система оценки, конструктор заданий и тд (потом напишем).</h3>
                    <Hidden mdDown>
                        <Grid item lg={12} data-aos="fade-up"
                              data-aos-anchor-placement="bottom-bottom">
                            <h4>Комфортная среда для создания собственных заданий!</h4>
                        </Grid>
                        <Grid item lg={12} data-aos="fade-up"
                              data-aos-anchor-placement="center-bottom">
                            <Buttom/>
                        </Grid>
                    </Hidden>
                </Grid>
                <Grid item xs={12} sm={12} md={4} lg={5} data-aos="fade-left"
                      data-aos-anchor-placement="center-bottom">
                    <div className={s.catSVG} dangerouslySetInnerHTML={{__html: catSvg}}/>
                </Grid>
                <Hidden lgUp>
                    <Grid item sm={12} md={12} data-aos="fade-up"
                          data-aos-anchor-placement="center-bottom" data-aos-delay='200'>
                        <h4>Комфортная среда для создания собственных заданий!</h4>
                    </Grid>
                    <Grid item xs={12} sm={12} md={4} data-aos="fade-up"
                          data-aos-anchor-placement="center-bottom" style={
                        {alignItems: 'center',}
                    }>
                        <Buttom/>
                    </Grid>
                </Hidden>
            </Grid>
        </>
    )
}