/**
 * Connetation Elements – GSAP Flip Gallery Modal
 * Grid of box-content divs; clicking moves the element into the modal via GSAP Flip.
 * Based on SimpleGridFlipModal CodePen.
 */

import { gsap } from 'gsap';
import { Flip } from 'gsap/Flip';

gsap.registerPlugin(Flip);

/**
 * @param {HTMLElement} container  .conn-flip-gallery element
 */
const initFlipGallery = (container) => {
    const modal        = container.querySelector('.conn-flip-gallery__modal');
    const modalContent = modal?.querySelector('.conn-flip-gallery__content');
    const modalOverlay = modal?.querySelector('.conn-flip-gallery__overlay');
    const btnPrev      = modal?.querySelector('.conn-flip-gallery__modal-prev');
    const btnNext      = modal?.querySelector('.conn-flip-gallery__modal-next');
    const boxes        = Array.from(container.querySelectorAll('.conn-flip-gallery__box'));
    const boxesContent = Array.from(container.querySelectorAll('.conn-flip-gallery__box-content'));

    if (!boxes.length || !modal || !modalContent) return;

    let boxIndex = undefined;

    // swipeConsumed must be declared before the click handlers that reference it
    let swipeConsumed = false;

    // ─── Open / close ─────────────────────────────────────────────────
    boxesContent.forEach((box, i) => {
        box.addEventListener('click', () => {
            if (swipeConsumed) { swipeConsumed = false; return; }

            if (boxIndex !== undefined) {
                // Close
                const state = Flip.getState(box);
                boxes[boxIndex].appendChild(box);
                boxIndex = undefined;
                gsap.to([modal, modalOverlay], { autoAlpha: 0, ease: 'power1.inOut', duration: 0.35 });
                Flip.from(state, {
                    duration: 0.7,
                    ease: 'power1.inOut',
                    absolute: true,
                    onComplete: () => gsap.set(box, { zIndex: 'auto' }),
                });
                gsap.set(box, { zIndex: 1002 });
            } else {
                // Open
                const state = Flip.getState(box);
                modalContent.appendChild(box);
                boxIndex = i;
                gsap.set(modal, { autoAlpha: 1 });
                Flip.from(state, { duration: 0.7, ease: 'power1.inOut' });
                gsap.to(modalOverlay, { autoAlpha: 0.65, duration: 0.35 });
            }
        });
    });

    // ─── Navigate prev / next ─────────────────────────────────────────
    const navigateTo = (direction) => {
        if (boxIndex === undefined) return;
        const current = modalContent.querySelector('.conn-flip-gallery__box-content');
        if (!current) return;

        const nextIndex   = ((boxIndex + direction) + boxes.length) % boxes.length;
        const nextContent = boxes[nextIndex].querySelector('.conn-flip-gallery__box-content');
        if (!nextContent) return;

        const stateOut = Flip.getState(current);
        const stateIn  = Flip.getState(nextContent);

        boxes[boxIndex].appendChild(current);
        boxIndex = nextIndex;
        modalContent.appendChild(nextContent);

        Flip.from(stateOut, { duration: 0.5, ease: 'power1.inOut', absolute: true });
        Flip.from(stateIn,  { duration: 0.5, ease: 'power1.inOut' });
    };

    if (btnPrev) btnPrev.addEventListener('click', () => navigateTo(-1));
    if (btnNext) btnNext.addEventListener('click', () => navigateTo(1));

    // ─── Overlay click closes ─────────────────────────────────────────
    if (modalOverlay) {
        modalOverlay.addEventListener('click', () => {
            if (boxIndex === undefined) return;
            const box = modalContent.querySelector('.conn-flip-gallery__box-content');
            if (box) box.click();
        });
    }

    // ─── Keyboard ────────────────────────────────────────────────────
    document.addEventListener('keydown', (e) => {
        if (boxIndex === undefined) return;
        if (e.key === 'Escape') {
            const box = modalContent.querySelector('.conn-flip-gallery__box-content');
            if (box) box.click();
        }
        if (e.key === 'ArrowRight') navigateTo(1);
        if (e.key === 'ArrowLeft')  navigateTo(-1);
    });

    // ─── Swipe / drag support ─────────────────────────────────────────
    // Use window for pointerup so fast swipes that end outside the modal
    // are still detected. isSwiping guards against unrelated pointer events.
    let swipeStartX = 0, isSwiping = false;

    modal.addEventListener('pointerdown', (e) => {
        swipeStartX = e.clientX;
        isSwiping   = true;
    }, { passive: true });

    window.addEventListener('pointerup', (e) => {
        if (!isSwiping) return;
        isSwiping = false;
        if (boxIndex === undefined) return;
        const dx = e.clientX - swipeStartX;
        if (Math.abs(dx) < 50) return;
        swipeConsumed = true;
        if (dx < 0) navigateTo(1);
        else        navigateTo(-1);
    }, { passive: true });

    window.addEventListener('pointercancel', () => { isSwiping = false; }, { passive: true });
};

// ─── Self-init ────────────────────────────────────────────────────────────────
function onReady(fn) {
    if (document.readyState !== 'loading') fn();
    else document.addEventListener('DOMContentLoaded', fn);
}

onReady(() => {
    document.querySelectorAll('.conn-flip-gallery').forEach(initFlipGallery);
});
