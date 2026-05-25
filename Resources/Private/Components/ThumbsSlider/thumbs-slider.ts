/**
 * Connetation Elements – Swiper Thumbnails Slider
 */

import Swiper from 'swiper';
import { Navigation, Autoplay, Thumbs, FreeMode } from 'swiper/modules';

class ThumbsSlider {
    private static readonly CONTAINER_SEL        = '.conn-thumbs-slider';
    private static readonly MAIN_SEL             = '.conn-thumbs-slider__main';
    private static readonly THUMBS_SEL           = '.conn-thumbs-slider__thumbs';
    private static readonly BTN_PREV_SEL         = '.conn-thumbs-slider__btn-prev';
    private static readonly BTN_NEXT_SEL         = '.conn-thumbs-slider__btn-next';
    private static readonly DATASET_THUMBS_PER_VIEW = 'thumbsPerView';
    private static readonly DATASET_AUTOPLAY        = 'autoplay';
    private static readonly DATASET_AUTOPLAY_DELAY  = 'autoplayDelay';
    private static readonly DATASET_NAVIGATION      = 'navigation';
    private static readonly ENABLED_VALUE           = '1';

    constructor(private readonly el: HTMLElement) {
        this.init();
    }

    private init(): void {
        const mainEl   = this.el.querySelector(ThumbsSlider.MAIN_SEL) as HTMLElement | null;
        const thumbsEl = this.el.querySelector(ThumbsSlider.THUMBS_SEL) as HTMLElement | null;
        if (!mainEl || !thumbsEl) return;

        const thumbsPerView = parseInt(this.el.dataset[ThumbsSlider.DATASET_THUMBS_PER_VIEW] || '4', 10);
        const autoplay      = this.el.dataset[ThumbsSlider.DATASET_AUTOPLAY] === ThumbsSlider.ENABLED_VALUE;
        const autoplayDelay = parseInt(this.el.dataset[ThumbsSlider.DATASET_AUTOPLAY_DELAY] || '4000', 10);
        const withNav       = this.el.dataset[ThumbsSlider.DATASET_NAVIGATION] === ThumbsSlider.ENABLED_VALUE;

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
                prevEl: this.el.querySelector(ThumbsSlider.BTN_PREV_SEL),
                nextEl: this.el.querySelector(ThumbsSlider.BTN_NEXT_SEL),
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
    }

    static initAll(): void {
        document.querySelectorAll<HTMLElement>(ThumbsSlider.CONTAINER_SEL)
            .forEach(el => new ThumbsSlider(el));
    }
}

document.addEventListener('DOMContentLoaded', () => ThumbsSlider.initAll());
