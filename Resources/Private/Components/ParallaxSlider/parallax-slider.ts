/**
 * Connetation Elements – Swiper Parallax Slider
 */

import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay, Parallax } from 'swiper/modules';
import type { SwiperOptions } from 'swiper/types';

const BG_TRAVEL = 0.23; // fraction of slider width the bg travels across full progress (0→1)

const initParallaxSlider = (container: HTMLElement): void => {
    const el = container.querySelector('.conn-parallax-slider__swiper') as HTMLElement | null;
    if (!el) return;

    const bgEl = el.querySelector<HTMLElement>('.conn-parallax-slider__bg');

    const autoplay       = container.dataset.autoplay === '1';
    const autoplayDelay  = parseInt(container.dataset.autoplayDelay || '5000', 10);
    const withNav        = container.dataset.navigation === '1';
    const withPagination = container.dataset.pagination === '1';

    const modules = [Parallax];
    if (withNav)        modules.push(Navigation);
    if (withPagination) modules.push(Pagination);
    if (autoplay)       modules.push(Autoplay);

    const updateBg = (swiper: Swiper, duration: number): void => {
        if (!bgEl) return;
        const offsetPx = -swiper.progress * BG_TRAVEL * swiper.width;
        bgEl.style.transitionDuration = `${duration}ms`;
        bgEl.style.transform = `translate3d(${offsetPx}px, 0, 0)`;
    };

    new Swiper(el, {
        modules,
        parallax: true,
        speed: 900,
        loop: false,

        navigation: withNav ? {
            prevEl: container.querySelector('.conn-parallax-slider__btn-prev'),
            nextEl: container.querySelector('.conn-parallax-slider__btn-next'),
        } : false,

        pagination: withPagination ? {
            el: container.querySelector('.conn-parallax-slider__pagination'),
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

        on: {
            progress(swiper) {
                updateBg(swiper, 0);
            },
            setTransition(swiper, duration) {
                updateBg(swiper, duration);
            },
        },
    } as SwiperOptions);
};

function onReady(fn: () => void): void {
    if (document.readyState !== 'loading') fn();
    else document.addEventListener('DOMContentLoaded', fn);
}

onReady(() => {
    document.querySelectorAll('.conn-parallax-slider').forEach(el => initParallaxSlider(el as HTMLElement));
});
