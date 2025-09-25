// https://stackoverflow.com/questions/27078285/simple-throttle-in-js
export function throttle(wait, func, options) {
    var context, args, result;
    var timeout = null;
    var previous = 0;
    if (!options) options = {};
    var later = function () {
        previous = options.leading === false ? 0 : Date.now();
        timeout = null;
        result = func.apply(context, args);
        if (!timeout) context = args = null;
    };
    return function () {
        var now = Date.now();
        if (!previous && options.leading === false) previous = now;
        var remaining = wait - (now - previous);
        context = this;
        args = arguments;
        if (remaining <= 0 || remaining > wait) {
            if (timeout) {
                clearTimeout(timeout);
                timeout = null;
            }
            previous = now;
            result = func.apply(context, args);
            if (!timeout) context = args = null;
        } else if (!timeout && options.trailing !== false) {
            timeout = setTimeout(later, remaining);
        }
        return result;
    };
}

export function debounce(func, wait, immediate) {
    var timeout;
    return function () {
        var context = this, args = arguments;
        var later = function () {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        var callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
}

export function isInViewport(el) {
    var rect = el.getBoundingClientRect();
    return rect.bottom <= (window.innerHeight || document.documentElement.clientHeight);
}

export function isEscape(e) {
    // keyCode is getting deprecated use key instead when supported, which makes this messy
    var evt = e || window.event;
    var isEscape = false;
    if ("key" in evt) {
        isEscape = (evt.key == "Escape" || evt.key == "Esc");
    } else {
        isEscape = (evt.keyCode == 27);
    }
    return isEscape;
}

export function trapFocus(trap, focusFirst = true) {
    const focusableElements = 'button:not([tabindex="-1"]), [href]:not([tabindex="-1"]), input, select, textarea, [tabindex]:not([tabindex="-1"]), iframe';
    const firstFocusableElement = trap.querySelectorAll(focusableElements)[0]; // get first element to be focused inside trap
    const focusableContent = trap.querySelectorAll(focusableElements);
    const lastFocusableElement = focusableContent[focusableContent.length - 1]; // get last element to be focused inside trap

    document.addEventListener('keydown', function (e) {
        let isTabPressed = e.key === 'Tab' || e.keyCode === 9;

        if (!isTabPressed) {
            return;
        }

        if (e.shiftKey) { // if shift key pressed for shift + tab combination
            if (document.activeElement === firstFocusableElement) {
                lastFocusableElement.focus(); // add focus for the last focusable element
                e.preventDefault();
            }
        } else { // if tab key is pressed
            if (document.activeElement === lastFocusableElement) { // if focused has reached to last focusable element then focus first focusable element after pressing tab
                firstFocusableElement.focus(); // add focus for the first focusable element
                e.preventDefault();
            }
        }
    });
    if (focusFirst) {
        setTimeout(() => {
            firstFocusableElement.focus();
        }, 350);
    }
}

export function returnFocus() {
    if (typeof window.returnFocusTo !== 'undefined') {
        window.returnFocusTo.focus();
    }
}
