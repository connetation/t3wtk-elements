/**
 * Connetation Elements – Swiper Thumbnails Slider
 */

import Swiper from 'swiper';
import { Navigation, Autoplay, Thumbs, FreeMode } from 'swiper/modules';

const initThumbsSlider = (container: HTMLElement): void => {
    const mainEl   = container.querySelector('.conn-thumbs-slider__main') as HTMLElement | null;
    const thumbsEl = container.querySelector('.conn-thumbs-slider__thumbs') as HTMLElement | null;
    if (!mainEl || !thumbsEl) return;

    const thumbsPerView = parseInt(container.dataset.thumbsPerView || '4', 10);
    const autoplay      = container.dataset.autoplay === '1';
    const autoplayDelay = parseInt(container.dataset.autoplayDelay || '4000', 10);
    const withNav       = container.dataset.navigation === '1';

    const thumbsSwiper = new Swiper(thumbsEl, {
        modules: [FreeMode],
        spaceBetween: 4,
        slidesPerView: thumbsPerView,
        freeMode: true,
        watchSlidesProgress: true,
    });

    const mainModules = [Thumbs];
    if (withNav)  mainModules.push(Navigation);
    if (autoplay) mainModules.push(Autoplay);

    new Swiper(mainEl, {
        modules: mainModules,
        loop: true,
        speed: 700,

        navigation: withNav ? {
            prevEl: container.querySelector('.conn-thumbs-slider__btn-prev'),
            nextEl: container.querySelector('.conn-thumbs-slider__btn-next'),
        } : false,

        autoplay: autoplay ? {
            delay: autoplayDelay,
            disableOnInteraction: false,
            pauseOnMouseEnter: true,
        } : false,

        thumbs: {
            swiper: thumbsSwiper,
        },

        a11y: {
            prevSlideMessage: 'Previous slide',
            nextSlideMessage: 'Next slide',
        },
    });
};

function onReady(fn: () => void): void {
    if (document.readyState !== 'loading') fn();
    else document.addEventListener('DOMContentLoaded', fn);
}

onReady(() => {
    document.querySelectorAll('.conn-thumbs-slider').forEach(el => initThumbsSlider(el as HTMLElement));
});
