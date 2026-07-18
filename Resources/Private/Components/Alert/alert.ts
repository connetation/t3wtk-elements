/**
 * Conn Alert/Notice Banner — fixed position
 * Static (inline) alerts need no JS at all — Bootstrap's own alert.js
 * (already loaded globally) handles the dismiss button.
 *
 */

import { gsap } from 'gsap';

class AlertFloat {

    private readonly toggle: HTMLElement | null;
    private pushResizeObserver: ResizeObserver | null = null;
    private readonly pushProperty: 'paddingTop' | 'paddingBottom' | null;
    private readonly pushTarget: HTMLElement | null;

    constructor(private readonly el: HTMLElement) {
        this.toggle = el.querySelector('.conn-alert-positioned__toggle');
        this.pushProperty = el.dataset.pushContent === '1'
            ? (el.dataset.edge === 'top' ? 'paddingTop' : 'paddingBottom')
            : null;
        this.pushTarget = this.pushProperty === 'paddingBottom'
            ? (document.querySelector<HTMLElement>('.footer') ?? document.body)
            : null;
        this.init();
        this.initPushContent();
    }

    private init(): void {
        if (this.toggle) {
            this.toggle.addEventListener('click', (event) => {
                event.stopPropagation();
                this.isOpen() ? this.close() : this.open();
            });

            document.addEventListener('click', (event) => {
                if (this.isOpen() && !this.el.contains(event.target as Node)) {
                    this.close();
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && this.isOpen()) {
                    this.close();
                }
            });
        }

        this.el.querySelector('.alert')?.addEventListener('closed.bs.alert', () => {
            if (this.pushProperty) {
                if (this.pushTarget) {
                    this.pushTarget.style[this.pushProperty] = '';
                }
                window.removeEventListener('resize', this.updatePushContent);
            }
            this.pushResizeObserver?.disconnect();
            this.el.remove();
        });
    }

    private initPushContent(): void {
        if (!this.pushProperty) {
            return;
        }

        this.updatePushContent = this.updatePushContent.bind(this);
        this.pushResizeObserver = new ResizeObserver(this.updatePushContent);
        this.pushResizeObserver.observe(this.el);
        window.addEventListener('resize', this.updatePushContent);
    }

    private updatePushContent(): void {
        const property = this.pushProperty!;
        const offsetSide = property === 'paddingTop' ? 'top' : 'bottom';

        const fixedNavSelector = property === 'paddingTop'
            ? '.navbar.fixed-top, .navbar.sticky-top'
            : '.navbar.fixed-bottom';
        const navHeight = document.querySelector(fixedNavSelector)
            ? parseFloat(getComputedStyle(document.documentElement).getPropertyValue('--conn-navbar-height')) || 0
            : 0;

        this.el.style[offsetSide] = '';
        const baseOffset = parseFloat(getComputedStyle(this.el)[offsetSide]) || 0;
        this.el.style[offsetSide] = navHeight ? `${baseOffset + navHeight}px` : '';

        if (!this.pushTarget) {
            return;
        }

        const cssProperty = property === 'paddingTop' ? 'padding-top' : 'padding-bottom';
        const target = this.pushTarget;

        target.style.removeProperty(cssProperty);
        const foreignBase = parseFloat(getComputedStyle(target)[property]) || 0;

        const edgeOffset = parseFloat(getComputedStyle(this.el)[offsetSide]) || 0;
        const ownHeight = this.el.getBoundingClientRect().height;

        target.style.setProperty(cssProperty, `${foreignBase + ownHeight + edgeOffset}px`, 'important');
    }

    private isOpen(): boolean {
        return this.el.classList.contains('is-open');
    }

    private open(): void {
        this.el.classList.add('is-open');
        this.toggle?.setAttribute('aria-expanded', 'true');
        this.morphToggle(true);
    }

    private close(): void {
        this.el.classList.remove('is-open');
        this.toggle?.setAttribute('aria-expanded', 'false');
        this.morphToggle(false);
    }

    private morphToggle(opening: boolean): void {
        if (!this.toggle) {
            return;
        }

        const radius = getComputedStyle(document.documentElement).getPropertyValue('--conn-radius').trim() || '0px';
        const isSquare = this.toggle.classList.contains('conn-alert-positioned__toggle--square');
        const restingRadius = isSquare ? radius : '50%';

        gsap.to(this.toggle, {
            borderRadius: opening ? radius : restingRadius,
            duration: 0.3,
            ease: 'power2.out',
        });
    }

}

import { registry } from '../Shared/registry';
registry.register('.conn-alert-positioned', el => new AlertFloat(el));
