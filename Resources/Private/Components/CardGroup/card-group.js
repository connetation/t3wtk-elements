/**
 * Connetation Elements – Bootstrap Card Group
 * GSAP entrance animation for card items.
 */

import { gsap } from 'gsap';

const initCardGroup = (container) => {
    const cards = container.querySelectorAll('.conn-card-group__card');
    if (!cards.length) return;

    gsap.from(cards, {
        opacity: 0,
        y: 20,
        duration: 0.4,
        stagger: 0.07,
        ease: 'power2.out',
        clearProps: 'all',
    });
};

// ─── Self-init ───────────────────────────────────────────────────────────────
function onReady(fn) {
    if (document.readyState !== 'loading') fn();
    else document.addEventListener('DOMContentLoaded', fn);
}

onReady(() => {
    document.querySelectorAll('.conn-card-group').forEach(initCardGroup);
});
