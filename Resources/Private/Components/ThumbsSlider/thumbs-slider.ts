/**
 * Connetation Elements – Swiper Thumbnails Slider
 */

import Swiper from 'swiper';
import { Navigation, Autoplay, Thumbs, FreeMode } from 'swiper/modules';
import { readAutoplay, buildSwiperAutoplay, ENABLED_VALUE } from '../Shared/autoplay';
import { animateSlide, prepareSlide } from '../Shared/slide-text-animation';
import { registry } from '../Shared/registry';

class ThumbsSlider {

    private static readonly MAIN_SEL             = '.conn-thumbs-slider__main';
    private static readonly THUMBS_SEL           = '.conn-thumbs-slider__thumbs';
    private static readonly BTN_PREV_SEL         = '.conn-thumbs-slider__btn-prev';
    private static readonly BTN_NEXT_SEL         = '.conn-thumbs-slider__btn-next';
    private static readonly DATASET_THUMBS_PER_VIEW = 'thumbsPerView';
    private static readonly DATASET_NAVIGATION      = 'navigation';

    constructor(private readonly el: HTMLElement) {
        this.init();
    }

    private init(): void {
        const mainEl   = this.el.querySelector(ThumbsSlider.MAIN_SEL) as HTMLElement | null;
        const thumbsEl = this.el.querySelector(ThumbsSlider.THUMBS_SEL) as HTMLElement | null;
        if (!mainEl || !thumbsEl) return;

        const thumbsPerView = parseInt(this.el.dataset[ThumbsSlider.DATASET_THUMBS_PER_VIEW] || '4', 10);
        const ap            = readAutoplay(this.el);
        const withNav       = this.el.dataset[ThumbsSlider.DATASET_NAVIGATION] === ENABLED_VALUE;

        const thumbsSwiper = new Swiper(thumbsEl, {
            modules: [FreeMode],
            spaceBetween: 4,
            slidesPerView: thumbsPerView,
            freeMode: true,
            watchSlidesProgress: true,
        });

        const mainModules = [Thumbs];
        if (withNav)    mainModules.push(Navigation);
        if (ap.enabled) mainModules.push(Autoplay);

        let lastRealIndex = -1;
        new Swiper(mainEl, {
            modules: mainModules,
            loop: true,
            speed: 700,

            navigation: withNav ? {
                prevEl: this.el.querySelector(ThumbsSlider.BTN_PREV_SEL),
                nextEl: this.el.querySelector(ThumbsSlider.BTN_NEXT_SEL),
            } : false,

            autoplay: buildSwiperAutoplay(ap.enabled, ap.delay),

            thumbs: {
                swiper: thumbsSwiper,
            },

            a11y: {
                prevSlideMessage: 'Previous slide',
                nextSlideMessage: 'Next slide',
            },

            on: {
                afterInit(swiper) {
                    lastRealIndex = swiper.realIndex;
                    animateSlide(swiper.slides[swiper.activeIndex] as HTMLElement);
                },
                slideChangeTransitionStart(swiper) {
                    if (swiper.realIndex === lastRealIndex) return;
                    prepareSlide(swiper.slides[swiper.activeIndex] as HTMLElement);
                },
                slideChangeTransitionEnd(swiper) {
                    if (swiper.realIndex === lastRealIndex) return;
                    lastRealIndex = swiper.realIndex;
                    animateSlide(swiper.slides[swiper.activeIndex] as HTMLElement);
                },
            },
        });
    }

}

registry.register('.conn-thumbs-slider', el => new ThumbsSlider(el));
