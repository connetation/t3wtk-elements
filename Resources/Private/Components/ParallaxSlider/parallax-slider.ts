/**
 * Connetation Elements – Swiper Parallax Slider
 */

import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay, Parallax } from 'swiper/modules';
import type { SwiperOptions } from 'swiper/types';

class ParallaxSlider {
    private static readonly CONTAINER_SEL        = '.conn-parallax-slider';
    private static readonly SWIPER_SEL           = '.conn-parallax-slider__swiper';
    private static readonly BG_SEL               = '.conn-parallax-slider__bg';
    private static readonly BTN_PREV_SEL         = '.conn-parallax-slider__btn-prev';
    private static readonly BTN_NEXT_SEL         = '.conn-parallax-slider__btn-next';
    private static readonly PAGINATION_SEL       = '.conn-parallax-slider__pagination';
    private static readonly DATASET_AUTOPLAY       = 'autoplay';
    private static readonly DATASET_AUTOPLAY_DELAY = 'autoplayDelay';
    private static readonly DATASET_NAVIGATION     = 'navigation';
    private static readonly DATASET_PAGINATION     = 'pagination';
    private static readonly ENABLED_VALUE          = '1';
    private static readonly BG_TRAVEL             = 0.23; // fraction of slider width the bg travels across full progress (0→1)

    constructor(private readonly el: HTMLElement) {
        this.init();
    }

    private init(): void {
        const swiperEl = this.el.querySelector(ParallaxSlider.SWIPER_SEL) as HTMLElement | null;
        if (!swiperEl) return;

        const bgEl = swiperEl.querySelector<HTMLElement>(ParallaxSlider.BG_SEL);

        const autoplay       = this.el.dataset[ParallaxSlider.DATASET_AUTOPLAY] === ParallaxSlider.ENABLED_VALUE;
        const autoplayDelay  = parseInt(this.el.dataset[ParallaxSlider.DATASET_AUTOPLAY_DELAY] || '5000', 10);
        const withNav        = this.el.dataset[ParallaxSlider.DATASET_NAVIGATION] === ParallaxSlider.ENABLED_VALUE;
        const withPagination = this.el.dataset[ParallaxSlider.DATASET_PAGINATION] === ParallaxSlider.ENABLED_VALUE;

        const modules = [Parallax];
        if (withNav)        modules.push(Navigation);
        if (withPagination) modules.push(Pagination);
        if (autoplay)       modules.push(Autoplay);

        const updateBg = (swiper: Swiper, duration: number): void => {
            if (!bgEl) return;
            const offsetPx = -swiper.progress * ParallaxSlider.BG_TRAVEL * swiper.width;
            bgEl.style.transitionDuration = `${duration}ms`;
            bgEl.style.transform = `translate3d(${offsetPx}px, 0, 0)`;
        };

        new Swiper(swiperEl, {
            modules,
            parallax: true,
            speed: 900,
            loop: false,

            navigation: withNav ? {
                prevEl: this.el.querySelector(ParallaxSlider.BTN_PREV_SEL),
                nextEl: this.el.querySelector(ParallaxSlider.BTN_NEXT_SEL),
            } : false,

            pagination: withPagination ? {
                el: this.el.querySelector(ParallaxSlider.PAGINATION_SEL),
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
    }

    static initAll(): void {
        document.querySelectorAll<HTMLElement>(ParallaxSlider.CONTAINER_SEL)
            .forEach(el => new ParallaxSlider(el));
    }
}

document.addEventListener('DOMContentLoaded', () => ParallaxSlider.initAll());
