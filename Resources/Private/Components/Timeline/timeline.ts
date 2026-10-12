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

import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { registry } from '../Shared/registry';

gsap.registerPlugin(ScrollTrigger);

class Timeline {

    constructor(private readonly el: HTMLElement) {
        this.init();
    }

    private init(): void {
        const items = Array.from(this.el.querySelectorAll<HTMLElement>('.conn-timeline__item'));
        const isScroll = this.el.dataset.layout === 'scroll';

        items.forEach((item) => {
            const fromVars: gsap.TweenVars = { opacity: 0, duration: 0.6, ease: 'power2.out' };

            if (isScroll) {
                fromVars.x = item.dataset.scrollSide === 'left' ? -40 : 40;
            } else {
                fromVars.y = 20;
                fromVars.duration = 0.5;
            }

            gsap.from(item, {
                ...fromVars,
                scrollTrigger: {
                    trigger: item,
                    start: 'top 85%',
                    once: true,
                },
            });
        });
    }

}

registry.register('.conn-timeline', el => new Timeline(el));
