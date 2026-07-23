/**
 * Conn Alert/Notice Banner
 * Static (inline, non-"remember dismissal") alerts need no JS at all —
 * Bootstrap's own alert.js (already loaded globally) handles the dismiss
 * button. AlertFloat below only applies to fixed/positioned alerts;
 * initDismissMemory applies to any alert with "remember dismissal" on.
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
        // Push the page content, never <body> itself — the navbar is a sibling
        // of #main-content, both direct children of body. Padding on body would
        // shift the (sticky/fixed) navbar down too, corrupting its own offset.
        const target = property === 'paddingBottom'
            ? (document.querySelector<HTMLElement>('.footer') ?? document.body)
            : (document.querySelector<HTMLElement>('#main-content') ?? document.body);

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
        // Measure the real rendered navbar — --conn-navbar-height is a static
        // 64px approximation that drifts from the actual height (logo size,
        // breakpoint, dropdown style, ...), leaving a gap or overlap.
        const navHeight = document.querySelector<HTMLElement>(fixedNavSelector)?.getBoundingClientRect().height ?? 0;

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

        // Square stays sharp-cornered in both states — nothing to morph.
        // Only circle morphs into a rounded rect as the panel opens.
        if (this.toggle.classList.contains('conn-alert-positioned__toggle--square')) {
            return;
        }

        const radius = getComputedStyle(document.documentElement).getPropertyValue('--conn-radius').trim() || '0px';

        gsap.to(this.toggle, {
            borderRadius: opening ? radius : '50%',
            duration: 0.3,
            ease: 'power2.out',
        });
    }

}

/**
 * If this alert was dismissed before, remove it before it's ever shown.
 * Otherwise, write the dismissal key on close so it stays gone next visit.
 * Listens on the close button's click directly rather than Bootstrap's
 * `closed.bs.alert` event — no dependency on the Alert plugin's internal
 * event lifecycle actually firing/being caught in time.
 */
function initDismissMemory(el: HTMLElement): void {
    const key = el.dataset.dismissKey;
    if (!key) {
        return;
    }

    const target = el.closest<HTMLElement>('.conn-alert-positioned') ?? el;

    if (localStorage.getItem(key)) {
        target.remove();
        return;
    }

    el.querySelector('[data-bs-dismiss="alert"]')?.addEventListener('click', () => {
        try {
            localStorage.setItem(key, '1');
        } catch {
            // Private browsing / storage disabled / quota exceeded — dismissal
            // just won't persist, the alert still closes normally either way.
        }
    });
}

import { registry } from '../Shared/registry';
// Must run before AlertFloat: an already-dismissed positioned alert needs to
// be gone before AlertFloat re-parents it into a stack container and
// reserves push-content padding for it — otherwise that state leaks (stale
// padding, orphaned stack) since we remove it here without AlertFloat's own
// teardown path.
registry.register('.conn-alert[data-remember-dismiss="1"]', initDismissMemory);
registry.register('.conn-alert-positioned', el => new AlertFloat(el));
