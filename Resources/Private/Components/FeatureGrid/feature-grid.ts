/**
 * Connetation Elements – Feature Grid
 * Sets CSS column count from data attribute.
 * Runs GSAP stagger entrance on scroll.
 * Steps variant: injects step numbers into icon boxes if they are empty.
 */

import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { registry } from '../Shared/registry';

gsap.registerPlugin(ScrollTrigger);

class FeatureGrid {

    private static readonly GRID_SEL  = '.conn-feature-grid__grid';
    private static readonly ITEM_SEL  = '.conn-feature-grid__item';
    private static readonly ICON_SEL  = '.conn-feature-grid__icon';

    constructor(private readonly el: HTMLElement) {
        this.init();
    }

    private init(): void {
        const grid = this.el.querySelector<HTMLElement>(FeatureGrid.GRID_SEL);
        if (grid) {
            const cols = parseInt(this.el.dataset.columns || '3', 10);
            grid.style.setProperty('--conn-fg-cols', String(cols));
        }

        const isSteps = this.el.classList.contains('conn-feature-grid--steps');
        if (isSteps) {
            this.injectStepNumbers();
        }

        const items = this.el.querySelectorAll(FeatureGrid.ITEM_SEL);
        if (!items.length) return;

        gsap.from(items, {
            opacity: 0,
            duration: 0.45,
            stagger: 0.08,
            ease: 'power2.out',
            scrollTrigger: {
                trigger: this.el,
                start: 'top 80%',
                once: true,
            },
        });
    }

    // Auto-number icon boxes in steps variant when they contain no content
    private injectStepNumbers(): void {
        const icons = Array.from(this.el.querySelectorAll<HTMLElement>(FeatureGrid.ICON_SEL));
        icons.forEach((icon, i) => {
            if (!icon.children.length && !icon.textContent?.trim()) {
                icon.textContent = String(i + 1);
            }
        });
    }

}

registry.register('.conn-feature-grid', el => new FeatureGrid(el));
