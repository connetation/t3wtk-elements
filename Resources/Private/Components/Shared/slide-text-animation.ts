/**
 * Connetation Elements – Slide Text Animation
 * Fires immediately on slide enter (no ScrollTrigger).
 * Imported by each slider component to hook into Swiper events.
 */

import { gsap } from 'gsap';
import { SplitText } from 'gsap/SplitText';

gsap.registerPlugin(SplitText);

const FUZZY_CHARSET  = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789@#$%&';
const HEADING_SEL    = 'h1, h2, h3, h4';
const BODY_SEL       = 'p, li';
// The CSS opacity:0 → 1 (--gsap.swiper-slide-active) toggle that this reveal
// step overrides sits on the *wrapping* body container (e.g.
// .conn-hero-slider__body), not on the <p>/<li> tags BODY_SEL finds inside
// it. A parent left at opacity:0 hides its children regardless of the
// children's own opacity, so the wrapper needs revealing too — title/eyebrow
// don't need this since their CSS class is on the element HEADING_SEL
// already targets directly.
const BODY_WRAPPER_SEL = '[class*="__body"]';

// Per-slide cleanup tracking
const activeSplits = new WeakMap<HTMLElement, SplitText[]>();
const activeRafs   = new WeakMap<HTMLElement, number>();

function clearSplits(slideEl: HTMLElement): void {
    const raf = activeRafs.get(slideEl);
    if (raf !== undefined) {
        cancelAnimationFrame(raf);
        activeRafs.delete(slideEl);
    }
    const splits = activeSplits.get(slideEl);
    if (splits) {
        splits.forEach(s => {
            const targets = [...(s.chars ?? []), ...(s.words ?? []), ...(s.lines ?? [])];
            gsap.killTweensOf(targets);
            gsap.set(targets, { clearProps: 'all' });
            s.revert();
        });
        activeSplits.delete(slideEl);
    }
}

export function animateSlide(slideEl: HTMLElement): void {
    const container = slideEl.querySelector<HTMLElement>('[data-slide-animation]');
    if (!container) return;
    const type = container.dataset.slideAnimation;
    if (!type) return;

    const headings = [...container.querySelectorAll<HTMLElement>(HEADING_SEL)];
    const body     = [...container.querySelectorAll<HTMLElement>(BODY_SEL)];
    const all      = [...headings, ...body];
    if (!all.length) return;

    clearSplits(slideEl);

    const rawDuration = parseInt(container.dataset.slideDuration ?? '0', 10);
    const customMs    = rawDuration > 0 ? rawDuration : 0;

    // Reveal each element via inline style before GSAP takes over.
    // CSS may hold them at opacity:0 (--gsap.swiper-slide-active rule) to
    // prevent them showing during the Swiper fade-in. Setting opacity:1 here
    // (inline beats CSS) lets the parent be visible while GSAP immediately
    // hides the split child spans via its from() initial render — no flash.
    all.forEach(el => {
        el.style.opacity    = '1';
        el.style.transform  = '';
        el.style.transition = 'none';
    });

    // Also reveal the body wrapper(s) themselves — see BODY_WRAPPER_SEL above.
    [...container.querySelectorAll<HTMLElement>(BODY_WRAPPER_SEL)].forEach(el => {
        el.style.opacity    = '1';
        el.style.transform  = '';
        el.style.transition = 'none';
    });

    const splits: SplitText[] = [];

    switch (type) {
        case 'words':
            all.forEach(el => {
                const s = SplitText.create(el, { type: 'words' });
                splits.push(s);
                gsap.from(s.words, {
                    opacity: 0, y: 24, duration: (customMs || 650) / 1000, stagger: 0.055,
                    ease: 'power3.out', onComplete: () => s.revert(),
                });
            });
            break;

        case 'lines':
            all.forEach(el => {
                const s = SplitText.create(el, { type: 'lines' });
                splits.push(s);
                gsap.from(s.lines, {
                    opacity: 0, y: 40, duration: (customMs || 700) / 1000, stagger: 0.12,
                    ease: 'power3.out', onComplete: () => s.revert(),
                });
            });
            break;

        case 'chars':
            all.forEach(el => {
                const s = SplitText.create(el, { type: 'chars,words' });
                splits.push(s);
                gsap.from(s.chars, {
                    opacity: 0, y: 16, rotationX: -45, transformOrigin: 'top center',
                    duration: (customMs || 400) / 1000, stagger: 0.028, ease: 'back.out(1.4)',
                    onComplete: () => s.revert(),
                });
            });
            break;

        case 'reveal':
            headings.forEach(el => {
                const s = SplitText.create(el, { type: 'chars,words' });
                splits.push(s);
                gsap.from(s.chars, {
                    opacity: 0, y: 60, scale: 0.8, duration: (customMs || 500) / 1000, stagger: 0.025,
                    ease: 'back.out(2.5)', onComplete: () => s.revert(),
                });
            });
            body.forEach(el => {
                const s = SplitText.create(el, { type: 'words' });
                splits.push(s);
                gsap.from(s.words, {
                    opacity: 0, y: 30, duration: (customMs || 450) / 1000, stagger: 0.04,
                    ease: 'back.out(1.7)', onComplete: () => s.revert(),
                });
            });
            break;

        case 'fuzzy': {
            const DURATION = customMs || 1100;
            all.forEach(el => {
                const s         = SplitText.create(el, { type: 'chars,words' });
                splits.push(s);
                const originals = s.chars.map(c => c.textContent ?? '');
                const resolved  = new Set<number>();
                const startTime = performance.now();

                const tick = (now: number) => {
                    const progress = Math.min((now - startTime) / DURATION, 1);
                    s.chars.forEach((char, i) => {
                        if (resolved.has(i)) return;
                        const resolveAt = 0.1 + (i / s.chars.length) * 0.75;
                        if (progress >= resolveAt) {
                            char.textContent = originals[i];
                            resolved.add(i);
                        } else if (originals[i].trim()) {
                            char.textContent = FUZZY_CHARSET[Math.floor(Math.random() * FUZZY_CHARSET.length)];
                        }
                    });
                    if (progress < 1) {
                        activeRafs.set(slideEl, requestAnimationFrame(tick));
                    } else {
                        activeRafs.delete(slideEl);
                        s.revert();
                    }
                };
                requestAnimationFrame(tick);
            });
            break;
        }
    }

    if (splits.length) activeSplits.set(slideEl, splits);
}

export function resetSlide(slideEl: HTMLElement): void {
    clearSplits(slideEl);
}
