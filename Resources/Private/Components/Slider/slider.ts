/*
 * This file is part of the Connetation TYPO3 Web Toolkit - T3WTK.
 *
 * Copyright (c) Connetation Web Engineering GmbH
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Connetation Elements – Swiper Card Slider
 */

import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay } from 'swiper/modules';
import { readAutoplay, buildSwiperAutoplay, ENABLED_VALUE } from '../Shared/autoplay';
import { animateSlide, prepareSlide } from '../Shared/slide-text-animation';
import { registry } from '../Shared/registry';
import { DESKTOP_MIN, TABLET_MIN } from '../Shared/devices';

interface SliderOptions {
    slidesPerView: number;
    /** 0 = automatic (derived from slidesPerView) */
    slidesPerViewTablet: number;
    slidesPerViewMobile: number;
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
            slidesPerViewTablet: parseInt(this.el.dataset.slidesPerViewTablet || '0', 10) || 0,
            slidesPerViewMobile: parseInt(this.el.dataset.slidesPerViewMobile || '0', 10) || 0,
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

        let lastRealIndex = -1;
        new Swiper(swiperEl, {
            modules,
            loop: options.loop,
            speed: 500,
            grabCursor: true,

            // Per device (breakpoints: Shared/devices.ts). Values not set by the
            // editor (0) keep the automatic 1 → 2 → 3 steps.
            slidesPerView: options.slidesPerViewMobile || 1,
            spaceBetween: 16,

            breakpoints: {
                576: {
                    slidesPerView: options.slidesPerViewMobile || Math.min(options.slidesPerView, 2),
                    spaceBetween: 20,
                },
                [TABLET_MIN]: {
                    slidesPerView: options.slidesPerViewTablet || Math.min(options.slidesPerView, 2),
                    spaceBetween: 20,
                },
                992: {
                    slidesPerView: options.slidesPerViewTablet || Math.min(options.slidesPerView, 3),
                    spaceBetween: 24,
                },
                [DESKTOP_MIN]: {
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

registry.register('.conn-slider', el => new Slider(el));
