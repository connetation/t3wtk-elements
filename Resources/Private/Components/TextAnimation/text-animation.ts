/**
 * Connetation Elements – GSAP Text Animation
 * Reads data-text-animation from each .frame and applies the chosen preset.
 * Editors pick the animation in the Appearance tab of every content element.
 */

import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { SplitText } from 'gsap/SplitText';

gsap.registerPlugin(ScrollTrigger, SplitText);

const HEADING_SEL = 'h1, h2, h3, h4, .ce-subheader';

// All <p> and <li> in the frame that aren't part of the heading block and don't
// belong to a nested child frame (e.g. grid column CEs have their own frames).
function getBodyElements(frame: HTMLElement): HTMLElement[] {
    return [...frame.querySelectorAll<HTMLElement>('p:not(.ce-subheader), li')]
        .filter(el => !el.closest('header') && el.closest('.frame') === frame);
}

const scrollTriggerConfig = (el: HTMLElement) => ({
    trigger: el,
    start: 'top 88%',
    once: true,
});

function whenVisible(el: HTMLElement, fn: () => void): void {
    ScrollTrigger.create({ ...scrollTriggerConfig(el), onEnter: fn });
}

// ── Words Fade In ─────────────────────────────────────────────────
// Each word slides up and fades in, staggered left-to-right.
function animateWords(headings: HTMLElement[]): void {
    headings.forEach((el) => {
        const split = SplitText.create(el, { type: 'words' });
        whenVisible(el, () => {
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
function animateLines(headings: HTMLElement[]): void {
    headings.forEach((el) => {
        const split = SplitText.create(el, { type: 'lines' });
        whenVisible(el, () => {
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
function animateChars(headings: HTMLElement[]): void {
    headings.forEach((el) => {
        const split = SplitText.create(el, { type: 'chars,words' });
        whenVisible(el, () => {
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
function init(): void {
    document.querySelectorAll<HTMLElement>('.frame[data-text-animation]').forEach((frame) => {
        const type = frame.dataset.textAnimation;
        if (!type) return;

        const headings = [...frame.querySelectorAll<HTMLElement>(HEADING_SEL)];
        const body     = getBodyElements(frame);

        // Swiper slides have their own CSS entrance animations — skip them.
        const all = [...headings, ...body].filter(el => !el.closest('.swiper-slide'));
        if (!all.length) return;

        switch (type) {
            case 'words': animateWords(all); break;
            case 'lines': animateLines(all); break;
            case 'chars': animateChars(all); break;
        }
    });
}

if (document.readyState !== 'loading') init();
else document.addEventListener('DOMContentLoaded', init);
