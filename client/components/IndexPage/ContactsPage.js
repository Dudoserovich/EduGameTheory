import React from "react";
import {Grid} from "@material-ui/core";
import AOS from "aos";
import s from './Style/ContactsPage.module.scss';

export default function TheoryPage() {
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
                <div className={s.marginStyle}>
                    <h2>Контакты</h2>
                    <Grid container spacing={2} style={{
                        justifyContent: 'space-around',
                        paddingTop: '20px',
                        marginBottom: '20px'

                    }}>
                        <Grid item sx={6} sm={6} md={5} lg={5} className={s.link}>
                            <a className={s.we} href="https://vk.com/liss_see"> Вконтакте: Liss_see</a>.
                        </Grid>
                        <Grid item sx={6} sm={6} md={5} lg={5} className={s.link}>
                            <a className={s.we} href="https://vk.com/egorhmell"> Вконтакте: Dudoserovich</a>.
                        </Grid>
                    </Grid>
                </div>
        </>
    )
}