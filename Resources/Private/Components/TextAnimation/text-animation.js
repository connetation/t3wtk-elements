/**
 * Connetation Elements – GSAP Text Animation
 * Reads data-text-animation from each .frame and applies the chosen preset.
 * Editors pick the animation in the Appearance tab of every content element.
 */

import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { SplitText } from 'gsap/SplitText';

gsap.registerPlugin(ScrollTrigger, SplitText);

const HEADING_SEL = 'h1, h2, h3, h4';
const BODY_SEL    = '.ce-bodytext p, .ce-bodytext li';

const trigger = (el) => ({
    trigger: el,
    start: 'top 88%',
    once: true,
});

// ── Words Fade In ─────────────────────────────────────────────────
// Each word slides up and fades in, staggered left-to-right.
function animateWords(headings) {
    headings.forEach((el) => {
        const split = SplitText.create(el, { type: 'words' });
        ScrollTrigger.create({
            ...trigger(el),
            onEnter() {
                gsap.from(split.words, {
                    opacity: 0,
                    y: 24,
                    duration: 0.65,
                    stagger: 0.055,
                    ease: 'power3.out',
                    onComplete: () => split.revert(),
                });
            },
        });
    });
}

// ── Lines Rise Up ─────────────────────────────────────────────────
// Each full line of text rises and fades in sequentially.
function animateLines(headings) {
    headings.forEach((el) => {
        const split = SplitText.create(el, { type: 'lines' });
        ScrollTrigger.create({
            ...trigger(el),
            onEnter() {
                gsap.from(split.lines, {
                    opacity: 0,
                    y: 40,
                    duration: 0.7,
                    stagger: 0.12,
                    ease: 'power3.out',
                    onComplete: () => split.revert(),
                });
            },
        });
    });
}

// ── Characters Cascade ────────────────────────────────────────────
// Characters drop in one-by-one with a subtle 3-D flip.
function animateChars(headings) {
    headings.forEach((el) => {
        const split = SplitText.create(el, { type: 'chars,words' });
        ScrollTrigger.create({
            ...trigger(el),
            onEnter() {
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
            },
        });
    });
}

// ── Fade Up ───────────────────────────────────────────────────────
// Whole elements (headings + body) fade and slide up as blocks.
// No text splitting — best for longer paragraphs.
function animateFadeUp(els) {
    els.forEach((el) => {
        gsap.from(el, {
            opacity: 0,
            y: 30,
            duration: 0.6,
            ease: 'power2.out',
            scrollTrigger: trigger(el),
        });
    });
}

// ── Init ──────────────────────────────────────────────────────────
function init() {
    document.querySelectorAll('.frame[data-text-animation]').forEach((frame) => {
        const type = frame.dataset.textAnimation;
        if (!type) return;

        const headings = [...frame.querySelectorAll(HEADING_SEL)];
        const body     = [...frame.querySelectorAll(BODY_SEL)];

        switch (type) {
            case 'words':   animateWords(headings); break;
            case 'lines':   animateLines(headings); break;
            case 'chars':   animateChars(headings); break;
            case 'fade-up': animateFadeUp([...headings, ...body]); break;
        }
    });
}

if (document.readyState !== 'loading') init();
else document.addEventListener('DOMContentLoaded', init);
