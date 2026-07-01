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
