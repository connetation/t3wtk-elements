/**
 * Connetation Elements – Swiper Card Slider
 */

import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay } from 'swiper/modules';
import { readAutoplay, buildSwiperAutoplay, ENABLED_VALUE } from '../Shared/autoplay';
import { registry } from '../Shared/registry';

interface SliderOptions {
    slidesPerView: number;
    loop: boolean;
    autoplay: boolean;
    autoplayDelay: number;
    withNav: boolean;
    withPagination: boolean;
}

class Slider {

    private static readonly SWIPER_SEL             = '.conn-slider__swiper';
    private static readonly BTN_PREV_SEL           = '.conn-slider__btn-prev';
    private static readonly BTN_NEXT_SEL           = '.conn-slider__btn-next';
    private static readonly PAGINATION_SEL         = '.conn-slider__pagination';
    private static readonly DATASET_SLIDES_PER_VIEW  = 'slidesPerView';
    private static readonly DATASET_LOOP             = 'loop';
    private static readonly DATASET_NAVIGATION       = 'navigation';
    private static readonly DATASET_PAGINATION       = 'pagination';

    constructor(private readonly el: HTMLElement) {
        this.init();
    }

    private init(): void {
        const swiperEl = this.el.querySelector(Slider.SWIPER_SEL) as HTMLElement | null;
        if (!swiperEl) return;

        const ap = readAutoplay(this.el);
        const options: SliderOptions = {
            slidesPerView:  parseInt(this.el.dataset[Slider.DATASET_SLIDES_PER_VIEW] || '3', 10),
            loop:           this.el.dataset[Slider.DATASET_LOOP] === ENABLED_VALUE,
            autoplay:       ap.enabled,
            autoplayDelay:  ap.delay,
            withNav:        this.el.dataset[Slider.DATASET_NAVIGATION] === ENABLED_VALUE,
            withPagination: this.el.dataset[Slider.DATASET_PAGINATION] === ENABLED_VALUE,
        };

        const modules = [];
        if (options.withNav)        modules.push(Navigation);
        if (options.withPagination) modules.push(Pagination);
        if (options.autoplay)       modules.push(Autoplay);

        new Swiper(swiperEl, {
            modules,
            loop: options.loop,
            speed: 500,
            grabCursor: true,

            slidesPerView: 1,
            spaceBetween: 16,

            breakpoints: {
                576: {
                    slidesPerView: Math.min(options.slidesPerView, 2),
                    spaceBetween: 20,
                },
                992: {
                    slidesPerView: Math.min(options.slidesPerView, 3),
                    spaceBetween: 24,
                },
                1200: {
                    slidesPerView: options.slidesPerView,
                    spaceBetween: 24,
                },
            },

            navigation: options.withNav ? {
                prevEl: this.el.querySelector(Slider.BTN_PREV_SEL),
                nextEl: this.el.querySelector(Slider.BTN_NEXT_SEL),
            } : false,

            pagination: options.withPagination ? {
                el: this.el.querySelector(Slider.PAGINATION_SEL),
                clickable: true,
            } : false,

            autoplay: buildSwiperAutoplay(options.autoplay, options.autoplayDelay),

            a11y: {
                prevSlideMessage: 'Previous slide',
                nextSlideMessage: 'Next slide',
            },
        });
    }

}

registry.register('.conn-slider', el => new Slider(el));
