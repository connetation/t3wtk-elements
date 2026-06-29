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
        items.forEach((item) => {
            gsap.from(item, {
                opacity: 0,
                y: 20,
                duration: 0.5,
                ease: 'power2.out',
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
