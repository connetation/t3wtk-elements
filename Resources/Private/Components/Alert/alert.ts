/**
 * Conn Alert/Notice Banner — fixed position
 * Static (inline) alerts need no JS at all — Bootstrap's own alert.js
 * (already loaded globally) handles the dismiss button.
 *
 */

import { gsap } from 'gsap';

interface PushState {
    property: 'paddingTop' | 'paddingBottom';
    target: HTMLElement;
    resizeObserver: ResizeObserver;
    update: () => void;
}

class AlertFloat {
    private static readonly stacks = new Map<string, HTMLElement>();
    private static readonly pushStates = new Map<string, PushState>();

    private readonly toggle: HTMLElement | null;
    private readonly stackKey: string;
    private readonly stackContainer: HTMLElement;

    constructor(private readonly el: HTMLElement) {
        this.toggle = el.querySelector('.conn-alert-positioned__toggle');
        this.stackKey = AlertFloat.computeStackKey(el);
        this.stackContainer = AlertFloat.getOrCreateStack(this.stackKey, el);
        this.stackContainer.appendChild(el);

        this.init();
        this.initPushContent();
    }

    private static computeStackKey(el: HTMLElement): string {
        const position = el.dataset.position ?? 'fixed-top';
        const isEdge = position === 'edge-left' || position === 'edge-right';
        return isEdge ? `${position}--${el.dataset.verticalAlign}` : position;
    }

    private static getOrCreateStack(key: string, sample: HTMLElement): HTMLElement {
        const existing = AlertFloat.stacks.get(key);
        if (existing) {
            return existing;
        }

        const stack = document.createElement('div');
        stack.className = `conn-alert-stack conn-alert-stack--${sample.dataset.position}`;
        if (sample.dataset.position === 'edge-left' || sample.dataset.position === 'edge-right') {
            stack.classList.add(`conn-alert-stack--align-${sample.dataset.verticalAlign}`);
        }
        document.body.appendChild(stack);
        AlertFloat.stacks.set(key, stack);
        return stack;
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
            const stackKey = this.stackKey;
            const stackContainer = this.stackContainer;
            this.el.remove();

            if (stackContainer.children.length === 0) {
                AlertFloat.teardownStack(stackKey, stackContainer);
            } else {
                AlertFloat.pushStates.get(stackKey)?.update();
            }
        });
    }

    private static teardownStack(key: string, container: HTMLElement): void {
        const push = AlertFloat.pushStates.get(key);
        if (push) {
            push.target.style[push.property] = '';
            push.resizeObserver.disconnect();
            window.removeEventListener('resize', push.update);
            AlertFloat.pushStates.delete(key);
        }
        container.remove();
        AlertFloat.stacks.delete(key);
    }

    private initPushContent(): void {
        if (this.el.dataset.pushContent !== '1' || AlertFloat.pushStates.has(this.stackKey)) {
            return;
        }

        const property: 'paddingTop' | 'paddingBottom' = this.el.dataset.edge === 'top' ? 'paddingTop' : 'paddingBottom';
        const target = property === 'paddingBottom'
            ? (document.querySelector<HTMLElement>('.footer') ?? document.body)
            : document.body;

        const stackContainer = this.stackContainer;
        const update = (): void => AlertFloat.updatePushContent(property, target, stackContainer);
        const resizeObserver = new ResizeObserver(update);
        resizeObserver.observe(stackContainer);
        window.addEventListener('resize', update);

        AlertFloat.pushStates.set(this.stackKey, { property, target, resizeObserver, update });
        update();
    }

    private static updatePushContent(
        property: 'paddingTop' | 'paddingBottom',
        target: HTMLElement,
        stackContainer: HTMLElement
    ): void {
        const offsetSide = property === 'paddingTop' ? 'top' : 'bottom';

        const fixedNavSelector = property === 'paddingTop'
            ? '.navbar.fixed-top, .navbar.sticky-top'
            : '.navbar.fixed-bottom';
        const navHeight = document.querySelector(fixedNavSelector)
            ? parseFloat(getComputedStyle(document.documentElement).getPropertyValue('--conn-navbar-height')) || 0
            : 0;

        stackContainer.style[offsetSide] = navHeight ? `${navHeight}px` : '';

        const cssProperty = property === 'paddingTop' ? 'padding-top' : 'padding-bottom';
        target.style.removeProperty(cssProperty);
        const foreignBase = parseFloat(getComputedStyle(target)[property]) || 0;

        const stackHeight = stackContainer.getBoundingClientRect().height;

        target.style.setProperty(cssProperty, `${foreignBase + stackHeight}px`, 'important');
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
