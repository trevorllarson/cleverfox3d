import { isEscape, returnFocus, trapFocus, throttle } from "./app/utils.js";

var returnFocusTo = window.returnFocusTo || null;
const breakpoints = {
    xs: 0,
    md: 768,
    lg: 1025,
    xl: 1536
};

/*
ADA
*/
document.body.addEventListener('keyup', e => {
    if (isEscape(e)) {
        // close things then return focus
        returnFocus();
    }
});
const isReduced = window.matchMedia(`(prefers-reduced-motion: reduce)`) === true || window.matchMedia(`(prefers-reduced-motion: reduce)`).matches === true;
if (!!isReduced) {
    const heroVideo = document.querySelector('.wp-block-pulp-hero video');
    if (heroVideo) {
        heroVideo.pause();
    }
}

/*
Mobile UI
*/
document.getElementById('site-navigation-open').addEventListener('click', e => {
    e.preventDefault();
    document.body.classList.add('menu-open');
});

document.getElementById('site-navigation-close').addEventListener('click', e => {
    e.preventDefault();
    document.body.classList.remove('menu-open');
});

/*
Scroll events
*/
// window.addEventListener('scroll', throttle(100, function () {
//     if (window.scrollY > 50) {
//         document.body.classList.add('is-scrolled');
//         return;
//     }
//     document.body.classList.remove('is-scrolled');
// }));
