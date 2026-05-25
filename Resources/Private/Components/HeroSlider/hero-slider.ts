/**
 * Connetation Elements – Swiper Hero Slider
 */

import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay, EffectFade } from 'swiper/modules';

interface HeroSliderOptions {
    effect: string;
    autoplay: boolean;
    autoplayDelay: number;
    withNav: boolean;
    withPagination: boolean;
}

class HeroSlider {
    private static readonly CONTAINER_SEL    = '.conn-hero-slider';
    private static readonly SWIPER_SEL       = '.conn-hero-slider__swiper';
    private static readonly BTN_PREV_SEL     = '.conn-hero-slider__btn-prev';
    private static readonly BTN_NEXT_SEL     = '.conn-hero-slider__btn-next';
    private static readonly PAGINATION_SEL   = '.conn-hero-slider__pagination';
    private static readonly DATASET_EFFECT         = 'effect';
    private static readonly DATASET_AUTOPLAY       = 'autoplay';
    private static readonly DATASET_AUTOPLAY_DELAY = 'autoplayDelay';
    private static readonly DATASET_NAVIGATION     = 'navigation';
    private static readonly DATASET_PAGINATION     = 'pagination';
    private static readonly EFFECT_FADE            = 'fade';
    private static readonly ENABLED_VALUE          = '1';

    constructor(private readonly el: HTMLElement) {
        this.init();
    }

    private init(): void {
        const swiperEl = this.el.querySelector(HeroSlider.SWIPER_SEL) as HTMLElement | null;
        if (!swiperEl) return;

        const options: HeroSliderOptions = {
            effect:         this.el.dataset[HeroSlider.DATASET_EFFECT] || HeroSlider.EFFECT_FADE,
            autoplay:       this.el.dataset[HeroSlider.DATASET_AUTOPLAY] === HeroSlider.ENABLED_VALUE,
            autoplayDelay:  parseInt(this.el.dataset[HeroSlider.DATASET_AUTOPLAY_DELAY] || '5000', 10),
            withNav:        this.el.dataset[HeroSlider.DATASET_NAVIGATION] === HeroSlider.ENABLED_VALUE,
            withPagination: this.el.dataset[HeroSlider.DATASET_PAGINATION] === HeroSlider.ENABLED_VALUE,
        };

        const modules = [EffectFade];
        if (options.withNav)        modules.push(Navigation);
        if (options.withPagination) modules.push(Pagination);
        if (options.autoplay)       modules.push(Autoplay);

        new Swiper(swiperEl, {
            modules,
            effect: options.effect,
            loop: true,
            speed: 800,
            fadeEffect: { crossFade: options.effect === HeroSlider.EFFECT_FADE },

            navigation: options.withNav ? {
                prevEl: this.el.querySelector(HeroSlider.BTN_PREV_SEL),
                nextEl: this.el.querySelector(HeroSlider.BTN_NEXT_SEL),
            } : false,

            pagination: options.withPagination ? {
                el: this.el.querySelector(HeroSlider.PAGINATION_SEL),
                clickable: true,
            } : false,

            autoplay: options.autoplay ? {
                delay: options.autoplayDelay,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            } : false,

            a11y: {
                prevSlideMessage: 'Previous slide',
                nextSlideMessage: 'Next slide',
            },
        });
    }

    static initAll(): void {
        document.querySelectorAll<HTMLElement>(HeroSlider.CONTAINER_SEL)
            .forEach(el => new HeroSlider(el));
    }
}

document.addEventListener('DOMContentLoaded', () => HeroSlider.initAll());
