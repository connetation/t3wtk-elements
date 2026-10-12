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
 * Connetation Elements – Stats / Counter
 * Animates numeric values from zero when they enter the viewport.
 */

import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { registry } from '../Shared/registry';

gsap.registerPlugin(ScrollTrigger);

class Stats {

    private static readonly ITEM_SEL  = '.conn-stats__item';
    private static readonly VALUE_SEL = '.conn-stats__value';

    constructor(private readonly el: HTMLElement) {
        this.init();
    }

    private init(): void {
        const items = Array.from(this.el.querySelectorAll<HTMLElement>(Stats.ITEM_SEL));
        if (!items.length) return;

        const duration = parseInt(this.el.dataset.duration || '2000', 10) / 1000;
        const countup  = this.el.dataset.countup === '1';

        gsap.from(items, {
            opacity: 0,
            duration: 0.5,
            stagger: 0.1,
            ease: 'power2.out',
            scrollTrigger: {
                trigger: this.el,
                start: 'top 80%',
                once: true,
            },
        });

        if (!countup) return;

        items.forEach((item) => {
            const valueEl = item.querySelector<HTMLElement>(Stats.VALUE_SEL);
            if (!valueEl) return;

            const target   = parseFloat(valueEl.dataset.target || valueEl.textContent || '0');
            const decimals = (String(target).split('.')[1] || '').length;

            ScrollTrigger.create({
                trigger: item,
                start: 'top 85%',
                once: true,
                onEnter: () => {
                    const proxy = { val: 0 };
                    gsap.to(proxy, {
                        val: target,
                        duration,
                        ease: 'power2.out',
                        onUpdate: () => {
                            valueEl.textContent = decimals
                                ? proxy.val.toFixed(decimals)
                                : Math.round(proxy.val).toLocaleString();
                        },
                    });
                },
            });
        });
    }

}

registry.register('.conn-stats', el => new Stats(el));
