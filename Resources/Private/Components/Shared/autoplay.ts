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
 * Shared autoplay utilities used by all components that support autoplay.
 *
 * - AutoplayController  — setInterval-based autoplay for CardStack / ShiftCarousel
 * - buildSwiperAutoplay — Swiper autoplay config object (HeroSlider / Slider / Parallax / Thumbs)
 * - readAutoplay        — reads data-autoplay / data-autoplay-delay from any element
 */

export const AUTOPLAY_DATASET = 'autoplay';
export const DELAY_DATASET    = 'autoplayDelay';
export const ENABLED_VALUE    = '1';
export const DEFAULT_DELAY    = 4000;

export function readAutoplay(el: HTMLElement): { enabled: boolean; delay: number } {
    return {
        enabled: el.dataset[AUTOPLAY_DATASET] === ENABLED_VALUE,
        delay:   parseInt(el.dataset[DELAY_DATASET] || String(DEFAULT_DELAY), 10),
    };
}

export function buildSwiperAutoplay(
    enabled: boolean,
    delay: number,
): { delay: number; disableOnInteraction: false; pauseOnMouseEnter: true } | false {
    if (!enabled) return false;
    return { delay, disableOnInteraction: false, pauseOnMouseEnter: true };
}

export class AutoplayController {
    readonly enabled: boolean;
    readonly delay: number;
    private timer: ReturnType<typeof setInterval> | null = null;

    constructor(el: HTMLElement, private readonly onTick: () => void) {
        const ap    = readAutoplay(el);
        this.enabled = ap.enabled;
        this.delay   = ap.delay;
    }

    start(): void {
        if (!this.enabled) return;
        this.stop();
        this.timer = setInterval(this.onTick, this.delay);
    }

    stop(): void {
        if (this.timer !== null) {
            clearInterval(this.timer);
            this.timer = null;
        }
    }

    // Pause on hover, resume on leave.
    attachHoverPause(target: HTMLElement): void {
        target.addEventListener('mouseenter', () => this.stop());
        target.addEventListener('mouseleave', () => this.start());
    }
}
