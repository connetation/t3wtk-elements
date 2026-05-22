/**
 * Connetation Elements – GSAP Card Stack
 * Stacked deck of cards; Flip plugin animates transitions.
 * Explicit JS positioning via positionCards() — not CSS nth-last-child.
 */

import { gsap } from 'gsap';
import { Flip } from 'gsap/Flip';

gsap.registerPlugin(Flip);

const OFFSET = 20; // px offset per stack level

/**
 * @param {HTMLElement} container  .conn-card-stack element
 */
const initCardStack = (container) => {
    const deck    = container.querySelector('.conn-card-stack__deck');
    const btnPrev = container.querySelector('.conn-card-stack__btn--prev');
    const btnNext = container.querySelector('.conn-card-stack__btn--next');
    const counter = container.querySelector('.conn-card-stack__counter');

    if (!deck) return;

    const getVisible = () =>
        Array.from(deck.querySelectorAll(':scope > .conn-card-stack__card'))
             .filter(c => c.style.display !== 'none');

    if (!getVisible().length) return;

    const totalCards = getVisible().length;
    let currentCard  = 1;
    let isAnimating  = false;

    const updateCounter = () => {
        if (counter) counter.textContent = `${currentCard} / ${totalCards}`;
    };

    // ─── Explicit positioning (last in array = front card) ───────────
    const positionCards = (cards) => {
        cards.forEach((card, i) => {
            const fromFront = cards.length - 1 - i;
            gsap.set(card, {
                left: fromFront * OFFSET,
                top: -(fromFront * OFFSET),
                zIndex: i + 1,
            });
        });
    };

    const setHeight = () => {
        const visible = getVisible();
        const front = visible[visible.length - 1];
        if (front) deck.style.height = front.offsetHeight + 'px';
    };

    positionCards(getVisible());
    setHeight();
    updateCounter();
    window.addEventListener('resize', () => { positionCards(getVisible()); setHeight(); });

    // ─── Advance ──────────────────────────────────────────────────────
    const advance = (forward) => {
        if (isAnimating) return;
        const cards = getVisible();
        if (cards.length <= 1) return;
        isAnimating = true;

        currentCard = forward
            ? (currentCard % totalCards) + 1
            : ((currentCard - 2 + totalCards) % totalCards) + 1;
        updateCounter();

        const state = Flip.getState(cards);
        let leavingCard;

        if (forward) {
            leavingCard = cards[cards.length - 1];
            const clone = leavingCard.cloneNode(true);
            deck.insertBefore(clone, deck.firstChild);
            leavingCard.style.display = 'none';
        } else {
            leavingCard = cards[0];
            const clone = leavingCard.cloneNode(true);
            deck.appendChild(clone);
            leavingCard.style.display = 'none';
        }

        const newVisible = getVisible();
        positionCards(newVisible);

        // Include leavingCard so Flip fires onLeave for it
        Flip.from(state, {
            targets: [...newVisible, leavingCard],
            duration: 0.5,
            ease: 'sine.inOut',
            onEnter: (elements) => gsap.from(elements, {
                duration: 0.3,
                yPercent: forward ? 20 : -20,
                opacity: 0,
                ease: 'expo.out',
            }),
            onLeave: (elements) => gsap.to(elements, {
                duration: 0.3,
                yPercent: 5,
                xPercent: forward ? -5 : 5,
                transformOrigin: forward ? 'bottom left' : 'bottom right',
                opacity: 0,
                ease: 'expo.out',
                onComplete() {
                    if (elements[0]?.parentNode) elements[0].parentNode.removeChild(elements[0]);
                    setHeight();
                },
            }),
            onComplete: () => { isAnimating = false; },
        });
    };

    // ─── Buttons + keyboard ───────────────────────────────────────────
    if (btnNext) btnNext.addEventListener('click', () => advance(true));
    if (btnPrev) btnPrev.addEventListener('click', () => advance(false));
    container.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowRight') advance(true);
        if (e.key === 'ArrowLeft')  advance(false);
    });

    // ─── Swipe / drag support ─────────────────────────────────────────
    // setPointerCapture routes all pointer events to the deck even when the
    // cursor leaves it mid-drag, giving reliable mouse drag on desktop.
    let swipeStartX = 0, isSwiping = false;

    const dragStart = (e) => {
        if (isAnimating) return;
        if (e.pointerType === 'mouse' && e.button !== 0) return;
        swipeStartX = e.clientX;
        isSwiping   = true;
        deck.setPointerCapture(e.pointerId);
        document.body.style.cursor = 'grabbing';
        e.preventDefault();
    };

    const dragEnd = (e) => {
        if (!isSwiping) return;
        isSwiping = false;
        document.body.style.cursor = '';
        const dx = e.clientX - swipeStartX;
        if (Math.abs(dx) < 50) return;
        advance(dx < 0);
    };

    const dragCancel = () => {
        isSwiping = false;
        document.body.style.cursor = '';
    };

    deck.addEventListener('pointerdown',   dragStart,  { passive: false });
    deck.addEventListener('pointerup',     dragEnd);
    deck.addEventListener('pointercancel', dragCancel);
};

// ─── Self-init ────────────────────────────────────────────────────────────────
function onReady(fn) {
    if (document.readyState !== 'loading') fn();
    else document.addEventListener('DOMContentLoaded', fn);
}

onReady(() => {
    document.querySelectorAll('.conn-card-stack').forEach(initCardStack);
});
