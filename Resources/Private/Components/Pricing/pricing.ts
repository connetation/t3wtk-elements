/**
 * Connetation Elements – Pricing Table
 * Sets CSS column count from data attribute and runs GSAP stagger entrance.
 */

import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { registry } from '../Shared/registry';

gsap.registerPlugin(ScrollTrigger);

class Pricing {

    private static readonly GRID_SEL = '.conn-pricing__grid';
    private static readonly CARD_SEL = '.conn-pricing__card';

    constructor(private readonly el: HTMLElement) {
        this.init();
    }

    private init(): void {
        const grid = this.el.querySelector<HTMLElement>(Pricing.GRID_SEL);
        if (grid) {
            const cols = parseInt(this.el.dataset.columns || '3', 10);
            grid.style.setProperty('--conn-pricing-cols', String(cols));
        }

        const cards = this.el.querySelectorAll(Pricing.CARD_SEL);
        if (!cards.length) return;

        gsap.from(cards, {
            opacity: 0,
            duration: 0.45,
            stagger: 0.1,
            ease: 'power2.out',
            scrollTrigger: {
                trigger: this.el,
                start: 'top 80%',
                once: true,
            },
        });
    }

}

registry.register('.conn-pricing', el => new Pricing(el));
