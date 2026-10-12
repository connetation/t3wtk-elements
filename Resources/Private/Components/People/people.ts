/*
 * This file is part of the Connetation TYPO3 Web Toolkit - T3WTK.
 *
 * Copyright (c) Connetation Web Engineering GmbH
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
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
 * Connetation Elements – People
 */

class People {

    private static readonly INTERACTIVE_SEL = [
        '.conn-people__flip-card',
        '.conn-people__team-flip',
        '.conn-people__reveal--sheet',
        '.conn-people__reveal--curtain',
        '.conn-people__reveal--spotlight',
    ].join(', ');

    constructor(private readonly el: HTMLElement) {
        this.init();
    }

    /**
     * Hover (and keyboard focus) previews the info; click / Enter / Space
     * toggles it pinned open (.is-flipped). Toggling it closed while the
     * pointer or focus is still on the card would otherwise leave it visible
     * via :hover / :focus-visible, so .is-dismissed suppresses those until
     * the pointer leaves or focus moves out of the card.
     */
    private init(): void {
        this.el.querySelectorAll<HTMLElement>(People.INTERACTIVE_SEL).forEach(card => {
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
    }

    private toggle(card: HTMLElement): void {
        const open = !card.classList.contains('is-flipped');
        card.classList.toggle('is-flipped', open);
        card.classList.toggle('is-dismissed', !open);
    }

}

import { registry } from '../Shared/registry';
registry.register('.conn-people', el => new People(el));
