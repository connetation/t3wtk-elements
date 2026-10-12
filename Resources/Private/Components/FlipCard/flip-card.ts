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
 * Connetation Elements – Flip Card
 * 3D flip is pure CSS; this handles the click/tap/keyboard pin and the
 * GSAP entrance animation.
 */

import { gsap } from 'gsap';

class FlipCard {

    private static readonly CARD_SEL = '.conn-flip-card__card';

    constructor(private readonly el: HTMLElement) {
        this.init();
    }

    /**
     * Hover (and keyboard focus) previews the back in hover-trigger mode;
     * click / Enter / Space toggles it pinned (.is-flipped) in both modes.
     * Unpinning while the pointer or focus is still on the card would
     * otherwise leave it flipped via :hover / :focus-visible, so .is-dismissed
     * suppresses those until the pointer leaves or focus moves out.
     */
    private init(): void {
        const cards = this.el.querySelectorAll<HTMLElement>(FlipCard.CARD_SEL);
        if (!cards.length) return;

        cards.forEach(card => {
            card.addEventListener('click', event => {
                if ((event.target as HTMLElement).closest('a')) {
                    return;
                }
                this.toggle(card);
            });

            card.addEventListener('keydown', event => {
                if (event.target !== card || (event.key !== 'Enter' && event.key !== ' ')) {
                    return;
                }
                event.preventDefault();
                this.toggle(card);
            });

            card.addEventListener('mouseleave', () => card.classList.remove('is-dismissed'));
            card.addEventListener('focusout', event => {
                if (!card.contains(event.relatedTarget as Node | null)) {
                    card.classList.remove('is-dismissed');
                }
            });
        });

        gsap.from(cards, {
            opacity: 0,
            duration: 0.4,
            stagger: 0.07,
            ease: 'power2.out',
            clearProps: 'opacity',
        });
    }

    private toggle(card: HTMLElement): void {
        const open = !card.classList.contains('is-flipped');
        card.classList.toggle('is-flipped', open);
        card.classList.toggle('is-dismissed', !open);
    }

}

import { registry } from '../Shared/registry';
registry.register('.conn-flip-card', el => new FlipCard(el));
