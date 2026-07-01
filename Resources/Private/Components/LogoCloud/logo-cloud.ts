/**
 * Connetation Elements – Logo Cloud
 * Grid: GSAP stagger entrance on scroll.
 * Marquee: duplicates items for seamless infinite scroll; pauses on hover.
 */

import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { registry } from '../Shared/registry';

gsap.registerPlugin(ScrollTrigger);

class LogoCloud {

    private static readonly TRACK_SEL = '.conn-logo-cloud__track';
    private static readonly ITEM_SEL  = '.conn-logo-cloud__item';

    constructor(private readonly el: HTMLElement) {
        this.init();
    }

    private init(): void {
        const layout = this.el.dataset.layout || 'grid';

        if (layout === 'marquee') {
            this.initMarquee();
        } else {
            this.initGrid();
        }
    }

    private initMarquee(): void {
        const track = this.el.querySelector<HTMLElement>(LogoCloud.TRACK_SEL);
        if (!track) return;

        // Duplicate original items so the marquee loops seamlessly at -50%
        const originals = Array.from(track.querySelectorAll<HTMLElement>(LogoCloud.ITEM_SEL));
        originals.forEach(item => track.appendChild(item.cloneNode(true)));

        // Set animation duration from data attribute (seconds)
        const speed = parseInt(this.el.dataset.speed || '30', 10);
        this.el.style.setProperty('--conn-marquee-duration', `${speed}s`);

        this.el.addEventListener('mouseenter', () => this.el.classList.add('conn-logo-cloud--paused'));
        this.el.addEventListener('mouseleave', () => this.el.classList.remove('conn-logo-cloud--paused'));
    }

    private initGrid(): void {
        const items = this.el.querySelectorAll(LogoCloud.ITEM_SEL);
        if (!items.length) return;

        gsap.from(items, {
            opacity: 0,
            duration: 0.4,
            stagger: 0.05,
            ease: 'power2.out',
            scrollTrigger: {
                trigger: this.el,
                start: 'top 80%',
                once: true,
            },
        });
    }

}

registry.register('.conn-logo-cloud', el => new LogoCloud(el));
