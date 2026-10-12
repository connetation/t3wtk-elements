/*
 * This file is part of the Connetation TYPO3 Web Toolkit - T3WTK.
 *
 * Copyright (c) Connetation Web Engineering GmbH
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
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
 * Connetation Elements – Swiper Hero Slider
 */

import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay, EffectFade } from 'swiper/modules';
import { readAutoplay, buildSwiperAutoplay, ENABLED_VALUE } from '../Shared/autoplay';
import { animateSlide, prepareSlide } from '../Shared/slide-text-animation';
import { registry } from '../Shared/registry';

interface HeroSliderOptions {
    effect: string;
    autoplay: boolean;
    autoplayDelay: number;
    withNav: boolean;
    withPagination: boolean;
}

class HeroSlider {

    private static readonly SWIPER_SEL       = '.conn-hero-slider__swiper';
    private static readonly BTN_PREV_SEL     = '.conn-hero-slider__btn-prev';
    private static readonly BTN_NEXT_SEL     = '.conn-hero-slider__btn-next';
    private static readonly PAGINATION_SEL   = '.conn-hero-slider__pagination';
    private static readonly DATASET_EFFECT      = 'effect';
    private static readonly DATASET_NAVIGATION  = 'navigation';
    private static readonly DATASET_PAGINATION  = 'pagination';
    private static readonly EFFECT_FADE         = 'fade';

    constructor(private readonly el: HTMLElement) {
        this.init();
    }

    private init(): void {
        const swiperEl = this.el.querySelector(HeroSlider.SWIPER_SEL) as HTMLElement | null;
        if (!swiperEl) return;

        const ap = readAutoplay(this.el);
        const options: HeroSliderOptions = {
            effect:         this.el.dataset[HeroSlider.DATASET_EFFECT] || HeroSlider.EFFECT_FADE,
            autoplay:       ap.enabled,
            autoplayDelay:  ap.delay,
            withNav:        this.el.dataset[HeroSlider.DATASET_NAVIGATION] === ENABLED_VALUE,
            withPagination: this.el.dataset[HeroSlider.DATASET_PAGINATION] === ENABLED_VALUE,
        };

        const modules = [EffectFade];
        if (options.withNav)        modules.push(Navigation);
        if (options.withPagination) modules.push(Pagination);
        if (options.autoplay)       modules.push(Autoplay);

        let lastRealIndex = -1;
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

registry.register('.conn-hero-slider', el => new HeroSlider(el));
