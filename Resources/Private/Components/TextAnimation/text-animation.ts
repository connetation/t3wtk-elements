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

const FUZZY_CHARSET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789@#$%&';

class TextAnimation {
    private static readonly HEADING_SEL = 'h1, h2, h3, h4, .ce-subheader';
    private static readonly EXCLUDE_SEL = '.swiper-slide';
    private static readonly ANIM_WORDS      = 'words';
    private static readonly ANIM_LINES      = 'lines';
    private static readonly ANIM_CHARS      = 'chars';
    private static readonly ANIM_REVEAL     = 'reveal';
    private static readonly ANIM_FUZZY      = 'fuzzy';

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

    // ── Split Reveal ──────────────────────────────────────────────────
    // Chars spring up from below — headings animate per-char,
    // body text animates per-word for readability.
    private static animateReveal(elements: HTMLElement[]): void {
        const headings = elements.filter(el => el.matches('h1, h2, h3, h4, .ce-subheader'));
        const body     = elements.filter(el => !el.matches('h1, h2, h3, h4, .ce-subheader'));

        headings.forEach((el) => {
            const split = SplitText.create(el, { type: 'chars,words' });
            TextAnimation.whenVisible(el, () => {
                gsap.from(split.chars, {
                    opacity: 0,
                    y: 60,
                    scale: 0.8,
                    duration: 0.5,
                    stagger: 0.025,
                    ease: 'back.out(2.5)',
                    onComplete: () => split.revert(),
                });
            });
        });

        body.forEach((el) => {
            const split = SplitText.create(el, { type: 'words' });
            TextAnimation.whenVisible(el, () => {
                gsap.from(split.words, {
                    opacity: 0,
                    y: 30,
                    duration: 0.45,
                    stagger: 0.04,
                    ease: 'back.out(1.7)',
                    onComplete: () => split.revert(),
                });
            });
        });
    }

    // ── Fuzzy Text ────────────────────────────────────────────────────
    // Each character scrambles through random chars before resolving
    // to its real value. Uses RAF — no additional GSAP plugin needed.
    private static animateFuzzy(elements: HTMLElement[]): void {
        const DURATION = 1100;

        elements.forEach((el) => {
            const split     = SplitText.create(el, { type: 'chars,words' });
            const originals = split.chars.map(c => c.textContent ?? '');

            TextAnimation.whenVisible(el, () => {
                const startTime = performance.now();
                const resolved  = new Set<number>();

                const tick = (now: number) => {
                    const progress = Math.min((now - startTime) / DURATION, 1);

                    split.chars.forEach((char, i) => {
                        if (resolved.has(i)) return;

                        // Each char resolves at a staggered point along the timeline.
                        const resolveAt = 0.1 + (i / split.chars.length) * 0.75;

                        if (progress >= resolveAt) {
                            char.textContent = originals[i];
                            resolved.add(i);
                        } else if (originals[i].trim()) {
                            char.textContent = FUZZY_CHARSET[Math.floor(Math.random() * FUZZY_CHARSET.length)];
                        }
                    });

                    if (progress < 1) {
                        requestAnimationFrame(tick);
                    } else {
                        split.revert();
                    }
                };

                requestAnimationFrame(tick);
            });
        });
    }

    // ── Per-frame init ────────────────────────────────────────────────
    static initFrame(frame: HTMLElement): void {
        const type = frame.dataset.textAnimation;
        if (!type) return;

        const headings = [...frame.querySelectorAll<HTMLElement>(TextAnimation.HEADING_SEL)];
        const body     = TextAnimation.getBodyElements(frame);

        // Swiper slides have their own CSS entrance animations — skip them.
        const all = [...headings, ...body].filter(el => !el.closest(TextAnimation.EXCLUDE_SEL));
        if (!all.length) return;

        switch (type) {
            case TextAnimation.ANIM_WORDS:      TextAnimation.animateWords(all);      break;
            case TextAnimation.ANIM_LINES:      TextAnimation.animateLines(all);      break;
            case TextAnimation.ANIM_CHARS:  TextAnimation.animateChars(all);  break;
            case TextAnimation.ANIM_REVEAL: TextAnimation.animateReveal(all); break;
            case TextAnimation.ANIM_FUZZY:      TextAnimation.animateFuzzy(all);      break;
        }
    }
}

import { registry } from '../Shared/registry';
registry.register('.frame[data-text-animation]', el => TextAnimation.initFrame(el));
