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
