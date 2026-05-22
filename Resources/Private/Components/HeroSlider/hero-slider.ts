/**
 * Connetation Elements – Swiper Hero Slider
 */

import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay, EffectFade } from 'swiper/modules';

const initHeroSlider = (container: HTMLElement): void => {
    const el = container.querySelector('.conn-hero-slider__swiper') as HTMLElement | null;
    if (!el) return;

    const effect         = container.dataset.effect || 'fade';
    const autoplay       = container.dataset.autoplay === '1';
    const autoplayDelay  = parseInt(container.dataset.autoplayDelay || '5000', 10);
    const withNav        = container.dataset.navigation === '1';
    const withPagination = container.dataset.pagination === '1';

    const modules = [EffectFade];
    if (withNav)        modules.push(Navigation);
    if (withPagination) modules.push(Pagination);
    if (autoplay)       modules.push(Autoplay);

    new Swiper(el, {
        modules,
        effect,
        loop: true,
        speed: 800,
        fadeEffect: { crossFade: effect === 'fade' },

        navigation: withNav ? {
            prevEl: container.querySelector('.conn-hero-slider__btn-prev'),
            nextEl: container.querySelector('.conn-hero-slider__btn-next'),
        } : false,

        pagination: withPagination ? {
            el: container.querySelector('.conn-hero-slider__pagination'),
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
    document.querySelectorAll('.conn-hero-slider').forEach(el => initHeroSlider(el as HTMLElement));
});
