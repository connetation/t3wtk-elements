/*
 * This file is part of the Connetation TYPO3 Web Toolkit - T3WTK.
 *
 * Copyright (c) Connetation Web Engineering GmbH
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

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
