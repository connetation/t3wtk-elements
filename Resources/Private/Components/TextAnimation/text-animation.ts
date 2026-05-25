/**
 * Connetation Elements – GSAP Text Animation
 * Reads data-text-animation from each .frame and applies the chosen preset.
 * Editors pick the animation in the Appearance tab of every content element.
 */

import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { SplitText } from 'gsap/SplitText';

gsap.registerPlugin(ScrollTrigger, SplitText);

interface ScrollTriggerConfig {
    trigger: HTMLElement;
    start: string;
    once: boolean;
}

class TextAnimation {
    private static readonly HEADING_SEL = 'h1, h2, h3, h4, .ce-subheader';
    private static readonly FRAME_SEL   = '.frame[data-text-animation]';
    private static readonly EXCLUDE_SEL = '.swiper-slide';
    private static readonly ANIM_WORDS  = 'words';
    private static readonly ANIM_LINES  = 'lines';
    private static readonly ANIM_CHARS  = 'chars';

    private static getBodyElements(frame: HTMLElement): HTMLElement[] {
        return [...frame.querySelectorAll<HTMLElement>('p:not(.ce-subheader), li')]
            .filter(el => !el.closest('header') && el.closest('.frame') === frame);
    }

    private static scrollTriggerConfig(el: HTMLElement): ScrollTriggerConfig {
        return { trigger: el, start: 'top 88%', once: true };
    }

    private static whenVisible(el: HTMLElement, fn: () => void): void {
        ScrollTrigger.create({ ...TextAnimation.scrollTriggerConfig(el), onEnter: fn });
    }

    // ── Words Fade In ─────────────────────────────────────────────────
    // Each word slides up and fades in, staggered left-to-right.
    private static animateWords(elements: HTMLElement[]): void {
        elements.forEach((el) => {
            const split = SplitText.create(el, { type: TextAnimation.ANIM_WORDS });
            TextAnimation.whenVisible(el, () => {
                gsap.from(split.words, {
                    opacity: 0,
                    y: 24,
                    duration: 0.65,
                    stagger: 0.055,
                    ease: 'power3.out',
                    onComplete: () => split.revert(),
                });
            });
        });
    }

    // ── Lines Rise Up ─────────────────────────────────────────────────
    // Each full line of text rises and fades in sequentially.
    private static animateLines(elements: HTMLElement[]): void {
        elements.forEach((el) => {
            const split = SplitText.create(el, { type: TextAnimation.ANIM_LINES });
            TextAnimation.whenVisible(el, () => {
                gsap.from(split.lines, {
                    opacity: 0,
                    y: 40,
                    duration: 0.7,
                    stagger: 0.12,
                    ease: 'power3.out',
                    onComplete: () => split.revert(),
                });
            });
        });
    }

    // ── Characters Cascade ────────────────────────────────────────────
    // Characters drop in one-by-one with a subtle 3-D flip.
    private static animateChars(elements: HTMLElement[]): void {
        elements.forEach((el) => {
            const split = SplitText.create(el, { type: `${TextAnimation.ANIM_CHARS},${TextAnimation.ANIM_WORDS}` });
            TextAnimation.whenVisible(el, () => {
                gsap.from(split.chars, {
                    opacity: 0,
                    y: 16,
                    rotationX: -45,
                    transformOrigin: 'top center',
                    duration: 0.4,
                    stagger: 0.028,
                    ease: 'back.out(1.4)',
                    onComplete: () => split.revert(),
                });
            });
        });
    }

    // ── Init ──────────────────────────────────────────────────────────
    static init(): void {
        document.querySelectorAll<HTMLElement>(TextAnimation.FRAME_SEL).forEach((frame) => {
            const type = frame.dataset.textAnimation;
            if (!type) return;

            const headings = [...frame.querySelectorAll<HTMLElement>(TextAnimation.HEADING_SEL)];
            const body     = TextAnimation.getBodyElements(frame);

            // Swiper slides have their own CSS entrance animations — skip them.
            const all = [...headings, ...body].filter(el => !el.closest(TextAnimation.EXCLUDE_SEL));
            if (!all.length) return;

            switch (type) {
                case TextAnimation.ANIM_WORDS: TextAnimation.animateWords(all); break;
                case TextAnimation.ANIM_LINES: TextAnimation.animateLines(all); break;
                case TextAnimation.ANIM_CHARS: TextAnimation.animateChars(all); break;
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', () => TextAnimation.init());
