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
