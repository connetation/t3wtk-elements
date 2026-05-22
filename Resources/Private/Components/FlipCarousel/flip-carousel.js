/**
 * Connetation Elements – GSAP Flip Carousel (Caterpillar)
 * Flex row of items; next/prev shifts the row with scale-in/out at the ends.
 * Based on FlipCaterpillar CodePen.
 */

import { gsap } from 'gsap';
import { Flip } from 'gsap/Flip';

gsap.registerPlugin(Flip);

/**
 * @param {HTMLElement} container  .conn-flip-carousel element
 */
const initFlipCarousel = (container) => {
    const track   = container.querySelector('.conn-flip-carousel__track');
    const btnNext = container.querySelector('.conn-flip-carousel__btn--next');
    const btnPrev = container.querySelector('.conn-flip-carousel__btn--prev');

    if (!track) return;

    const cols = parseInt(track.dataset.items, 10) || 4;
    track.style.setProperty('--conn-items', cols);

    // Snapshot all item HTML upfront; DOM is rebuilt from this for circular reuse
    const allItems = Array.from(track.querySelectorAll('.conn-flip-carousel__item'))
        .map(el => el.innerHTML);
    const total = allItems.length;
    if (total < 2) return;

    const visibleCount = Math.min(cols, total);

    // Rebuild track with only the initial visible window
    track.innerHTML = '';
    for (let i = 0; i < visibleCount; i++) {
        const el = document.createElement('div');
        el.className = 'conn-flip-carousel__item';
        el.innerHTML = allItems[i];
        track.appendChild(el);
    }

    let startIndex = 0;
    let isAnimating = false;

    const makeItem = (index) => {
        const el = document.createElement('div');
        el.className = 'conn-flip-carousel__item';
        el.innerHTML = allItems[((index % total) + total) % total];
        return el;
    };

    // ─── Advance ──────────────────────────────────────────────────────
    const advance = (forward) => {
        if (isAnimating) return;
        const items = Array.from(track.querySelectorAll('.conn-flip-carousel__item'));
        isAnimating = true;

        const state = Flip.getState(items);

        if (forward) {
            const newCard = makeItem(startIndex + visibleCount);
            track.append(newCard);
            items[0].classList.add('hide');
            startIndex = (startIndex + 1) % total;
        } else {
            const newCard = makeItem(startIndex - 1);
            track.prepend(newCard);
            items[items.length - 1].classList.add('hide');
            startIndex = ((startIndex - 1) % total + total) % total;
        }

        Flip.from(state, {
            targets: track.querySelectorAll('.conn-flip-carousel__item'),
            duration: 0.5,
            ease: 'power2.inOut',
            absoluteOnLeave: true,
            onComplete: () => { isAnimating = false; },
            onEnter: (els) => gsap.from(els, {
                duration: 0.4,
                opacity: 0,
                scale: 0,
                transformOrigin: forward ? 'bottom right' : 'bottom left',
                ease: 'back.out(1.4)',
            }),
            onLeave: (els) => gsap.to(els, {
                duration: 0.3,
                opacity: 0,
                scale: 0,
                transformOrigin: forward ? 'bottom left' : 'bottom right',
                onComplete: () => {
                    els.forEach(el => { if (el.parentNode) el.parentNode.removeChild(el); });
                },
            }),
        });
    };

    // ─── Buttons ──────────────────────────────────────────────────────
    if (btnNext) btnNext.addEventListener('click', () => advance(true));
    if (btnPrev) btnPrev.addEventListener('click', () => advance(false));

    // ─── Swipe / drag support ─────────────────────────────────────────
    let swipeStartX = 0, isSwiping = false;

    track.addEventListener('pointerdown', (e) => {
        swipeStartX = e.clientX;
        isSwiping   = true;
    }, { passive: true });

    window.addEventListener('pointerup', (e) => {
        if (!isSwiping) return;
        isSwiping = false;
        const dx = e.clientX - swipeStartX;
        if (Math.abs(dx) < 50) return;
        if (dx < 0) advance(true);
        else        advance(false);
    }, { passive: true });

    window.addEventListener('pointercancel', () => { isSwiping = false; }, { passive: true });
};

// ─── Self-init ────────────────────────────────────────────────────────────────
function onReady(fn) {
    if (document.readyState !== 'loading') fn();
    else document.addEventListener('DOMContentLoaded', fn);
}

onReady(() => {
    document.querySelectorAll('.conn-flip-carousel').forEach(initFlipCarousel);
});
