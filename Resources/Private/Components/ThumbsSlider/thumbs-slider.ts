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
 * Connetation Elements – Swiper Thumbnails Slider
 */

import Swiper from 'swiper';
import { Navigation, Autoplay, Thumbs, FreeMode } from 'swiper/modules';
import { readAutoplay, buildSwiperAutoplay, ENABLED_VALUE } from '../Shared/autoplay';
import { animateSlide, prepareSlide } from '../Shared/slide-text-animation';
import { registry } from '../Shared/registry';
import { DESKTOP_MIN, TABLET_MIN } from '../Shared/devices';

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
        // Per device (breakpoints: Shared/devices.ts)
        const thumbsTablet  = parseInt(this.el.dataset.thumbsPerViewTablet || '', 10) || thumbsPerView;
        const thumbsMobile  = parseInt(this.el.dataset.thumbsPerViewMobile || '', 10) || thumbsTablet;
        const ap            = readAutoplay(this.el);
        const withNav       = this.el.dataset[ThumbsSlider.DATASET_NAVIGATION] === ENABLED_VALUE;

        const thumbsSwiper = new Swiper(thumbsEl, {
            modules: [FreeMode],
            spaceBetween: 4,
            slidesPerView: thumbsMobile,
            breakpoints: {
                [TABLET_MIN]:  { slidesPerView: thumbsTablet },
                [DESKTOP_MIN]: { slidesPerView: thumbsPerView },
            },
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
