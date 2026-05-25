/**
 * Connetation Elements – Bootstrap Card Group
 * GSAP entrance animation for card items.
 */

import { gsap } from 'gsap';

class CardGroup {
    private static readonly CONTAINER_SEL = '.conn-card-group';
    private static readonly CARD_SEL      = '.conn-card-group__card';

    constructor(private readonly el: HTMLElement) {
        this.init();
    }

    private init(): void {
        const cards = this.el.querySelectorAll(CardGroup.CARD_SEL);
        if (!cards.length) return;

        gsap.from(cards, {
            opacity: 0,
            y: 20,
            duration: 0.4,
            stagger: 0.07,
            ease: 'power2.out',
            clearProps: 'all',
        });
    }

    static initAll(): void {
        document.querySelectorAll<HTMLElement>(CardGroup.CONTAINER_SEL)
            .forEach(el => new CardGroup(el));
    }
}

document.addEventListener('DOMContentLoaded', () => CardGroup.initAll());
