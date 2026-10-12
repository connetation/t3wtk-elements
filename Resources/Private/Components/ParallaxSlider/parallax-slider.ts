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
 * Connetation Elements – Swiper Parallax Slider
 */

import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay, Parallax } from 'swiper/modules';
import type { SwiperOptions } from 'swiper/types';
import { readAutoplay, buildSwiperAutoplay, ENABLED_VALUE } from '../Shared/autoplay';
import { animateSlide, prepareSlide } from '../Shared/slide-text-animation';
import { registry } from '../Shared/registry';

class ParallaxSlider {

    private static readonly SWIPER_SEL           = '.conn-parallax-slider__swiper';
    private static readonly BG_SEL               = '.conn-parallax-slider__bg';
    private static readonly BTN_PREV_SEL         = '.conn-parallax-slider__btn-prev';
    private static readonly BTN_NEXT_SEL         = '.conn-parallax-slider__btn-next';
    private static readonly PAGINATION_SEL       = '.conn-parallax-slider__pagination';
    private static readonly DATASET_NAVIGATION   = 'navigation';
    private static readonly DATASET_PAGINATION   = 'pagination';
    private static readonly BG_TRAVEL            = 0.23; // fraction of slider width the bg travels across full progress (0→1)

    constructor(private readonly el: HTMLElement) {
        this.init();
    }

    private init(): void {
        const swiperEl = this.el.querySelector(ParallaxSlider.SWIPER_SEL) as HTMLElement | null;
        if (!swiperEl) return;

        const bgEl = swiperEl.querySelector<HTMLElement>(ParallaxSlider.BG_SEL);

        const ap             = readAutoplay(this.el);
        const withNav        = this.el.dataset[ParallaxSlider.DATASET_NAVIGATION] === ENABLED_VALUE;
        const withPagination = this.el.dataset[ParallaxSlider.DATASET_PAGINATION] === ENABLED_VALUE;

        const modules = [Parallax];
        if (withNav)        modules.push(Navigation);
        if (withPagination) modules.push(Pagination);
        if (ap.enabled)     modules.push(Autoplay);

        let lastRealIndex = -1;

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

            autoplay: buildSwiperAutoplay(ap.enabled, ap.delay),

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
        } as SwiperOptions);
    }

}

registry.register('.conn-parallax-slider', el => new ParallaxSlider(el));
