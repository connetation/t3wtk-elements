/**
 * Connetation Elements – Swiper Card Slider
 */

import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay } from 'swiper/modules';

const initSlider = (container: HTMLElement): void => {
    const el = container.querySelector('.conn-slider__swiper') as HTMLElement | null;
    if (!el) return;

    const slidesPerView  = parseInt(container.dataset.slidesPerView || '3', 10);
    const loop           = container.dataset.loop === '1';
    const autoplay       = container.dataset.autoplay === '1';
    const autoplayDelay  = parseInt(container.dataset.autoplayDelay || '4000', 10);
    const withNav        = container.dataset.navigation === '1';
    const withPagination = container.dataset.pagination === '1';

    const modules = [];
    if (withNav)        modules.push(Navigation);
    if (withPagination) modules.push(Pagination);
    if (autoplay)       modules.push(Autoplay);

    new Swiper(el, {
        modules,
        loop,
        speed: 500,
        grabCursor: true,

        slidesPerView: 1,
        spaceBetween: 16,

        breakpoints: {
            576: {
                slidesPerView: Math.min(slidesPerView, 2),
                spaceBetween: 20,
            },
            992: {
                slidesPerView: Math.min(slidesPerView, 3),
                spaceBetween: 24,
            },
            1200: {
                slidesPerView,
                spaceBetween: 24,
            },
        },

        navigation: withNav ? {
            prevEl: container.querySelector('.conn-slider__btn-prev'),
            nextEl: container.querySelector('.conn-slider__btn-next'),
        } : false,

        pagination: withPagination ? {
            el: container.querySelector('.conn-slider__pagination'),
            clickable: true,
        } : false,

        autoplay: autoplay ? {
            delay: autoplayDelay,
            disableOnInteraction: false,
            pauseOnMouseEnter: true,
        } : false,

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
    document.querySelectorAll('.conn-slider').forEach(el => initSlider(el as HTMLElement));
});
