import React from "react";
import {Grid} from "@material-ui/core";
import AOS from "aos";
import achiv1 from "./svg/achiv2.svg";
import achiv2 from "./svg/achiv1.svg";
import achiv3 from "./svg/achiv3.svg";

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
            <h2>НАГРАДЫ</h2>
            <h4>Получайте различные награды за прохождение заданий!</h4>
            <Grid container spacing={2} style={{
                justifyContent: 'space-evenly',
                paddingTop: '20px',
                marginBottom: '20px'

            }}>
                <Grid item xs={2} sm={2} md={2} lg={2} data-aos="fade-right"
                      data-aos-anchor-placement="center-bottom" style={{
                    marginTop: 'auto',
                    marginBottom: 'auto',

                }}>
                    <div data-aos="fade-left" data-aos-anchor-placement="top-bottom"
                         dangerouslySetInnerHTML={{__html: achiv1}}/>
                    <h5>10 побед!</h5>
                </Grid>
                <Grid item xs={2} sm={2} md={2} lg={2} data-aos="fade-top"
                      data-aos-anchor-placement="center-bottom">
                    <div data-aos="fade-left" data-aos-anchor-placement="top-bottom"
                         dangerouslySetInnerHTML={{__html: achiv2}}/>
                    <h5>10 побед!</h5>
                </Grid>
                <Grid item xs={2} sm={2} md={2} lg={2} data-aos="fade-left"
                      data-aos-anchor-placement="center-bottom" style={{
                    marginTop: 'auto',
                    marginBottom: 'auto',

                }}>
                    <div data-aos="fade-left" data-aos-anchor-placement="top-bottom"
                         dangerouslySetInnerHTML={{__html: achiv3}}/>
                    <h5>10 побед!</h5>
                </Grid>
            </Grid>
            <h4>Примите участие в поиске секретных наград!</h4>
        </>
    )
}