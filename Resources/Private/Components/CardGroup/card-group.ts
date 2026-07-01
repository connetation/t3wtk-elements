/**
 * Connetation Elements – Bootstrap Card Group
 * GSAP entrance animation for card items.
 */

import { gsap } from 'gsap';

class CardGroup {

    private static readonly CARD_SEL      = '.conn-card-group__card';

    constructor(private readonly el: HTMLElement) {
        this.init();
    }

    private init(): void {
        const cards = this.el.querySelectorAll(CardGroup.CARD_SEL);
        if (!cards.length) return;

        gsap.from(cards, {
            opacity: 0,
            duration: 0.4,
            stagger: 0.07,
            ease: 'power2.out',
            clearProps: 'all',
        });
    }

}

import { registry } from '../Shared/registry';
registry.register('.conn-card-group', el => new CardGroup(el));
