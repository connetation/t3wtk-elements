/**
 * Conn Bootstrap Tabs
 * Core show/hide behaviour is handled natively by Bootstrap 5 via
 * data-bs-toggle / data-bs-target attributes — no extra code needed.
 *
 * This module adds two enhancements:
 *  - a sliding active-indicator bar under the nav-tabs style (skipped for
 *    the nav-pills and connected styles, whose active tab already stands out
 *    by its own background)
 *  - session-state persistence of the last active tab, so returning to the
 *    page (e.g. via browser back) keeps the previously selected pane open
 *  - on mobile, where the nav scrolls horizontally instead of wrapping:
 *    is-overflow-start/-end classes for the edge fades, a one-time "nudge"
 *    hinting that the row swipes, and keeping the active tab scrolled into view
 */

class Tabs {

    private static readonly EVENT_SHOWN        = 'shown.bs.tab';
    private static readonly STORAGE_KEY_PREFIX = 'conn-tabs:';
    private static readonly ACTIVE_TAB_SEL     = '.nav-link.active';
    private static readonly NUDGE_DISTANCE     = 56;

    private nav: HTMLElement | null;
    private indicator: HTMLSpanElement | null = null;
    private storageKey = '';

    constructor(private readonly el: HTMLElement) {
        this.nav = el.querySelector('.conn-tabs__nav');
        this.storageKey = `${Tabs.STORAGE_KEY_PREFIX}${el.id}`;
        this.init();
    }

    private init(): void {
        this.restoreActiveTab();

        if (this.nav?.classList.contains('nav-tabs') && !this.nav.classList.contains('conn-tabs__nav--connected')) {
            this.initIndicator();
        }

        this.el.addEventListener(Tabs.EVENT_SHOWN, (event) => {
            this.persistActiveTab(event.target as HTMLElement);
            this.moveIndicator(event.target as HTMLElement);
            this.scrollIntoView(event.target as HTMLElement, 'smooth');
        });

        this.initOverflow();
    }

    private initOverflow(): void {
        const nav = this.nav;
        if (!nav) return;

        const update = () => {
            const max = nav.scrollWidth - nav.clientWidth;
            nav.classList.toggle('is-overflow-start', nav.scrollLeft > 1);
            nav.classList.toggle('is-overflow-end', nav.scrollLeft < max - 1);
        };

        nav.addEventListener('scroll', update, { passive: true });
        new ResizeObserver(() => {
            update();
            const activeBtn = nav.querySelector<HTMLElement>(Tabs.ACTIVE_TAB_SEL);
            if (activeBtn) this.moveIndicator(activeBtn);
        }).observe(nav);

        const activeBtn = nav.querySelector<HTMLElement>(Tabs.ACTIVE_TAB_SEL);
        if (activeBtn) this.scrollIntoView(activeBtn, 'instant');
        update();

        this.initNudge();
    }

    /**
     * The first time an overflowing nav scrolls into view, slide it a little
     * to the left and spring back — the familiar "this row swipes" cue.
     * Skipped for reduced motion, or once the user has touched the row.
     */
    private initNudge(): void {
        const nav = this.nav;
        if (!nav || matchMedia('(prefers-reduced-motion: reduce)').matches) return;

        let touched = false;
        const markTouched = () => { touched = true; };
        nav.addEventListener('pointerdown', markTouched, { once: true });
        nav.addEventListener('wheel', markTouched, { once: true, passive: true });

        const observer = new IntersectionObserver((entries) => {
            if (!entries[0]?.isIntersecting) return;
            observer.disconnect();
            if (touched || !nav.classList.contains('is-overflow-end')) return;
            window.setTimeout(() => { if (!touched) this.nudge(); }, 400);
        }, { threshold: 1 });
        observer.observe(nav);
    }

    private nudge(): void {
        const nav = this.nav!;
        const from = nav.scrollLeft;
        const distance = Math.min(Tabs.NUDGE_DISTANCE, nav.scrollWidth - nav.clientWidth - from);
        const duration = 900;
        const t0 = performance.now();

        // Out with ease-out over the first 40 %, back with ease-in-out after
        const step = (now: number) => {
            const t = Math.min((now - t0) / duration, 1);
            const p = t < 0.4
                ? 1 - (1 - t / 0.4) ** 3
                : 1 - (u => (u < 0.5 ? 2 * u * u : 1 - (-2 * u + 2) ** 2 / 2))((t - 0.4) / 0.6);
            nav.scrollLeft = from + distance * p;
            if (t < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
    }

    /** Horizontal-only scroll of the nav, so the page itself never jumps. */
    private scrollIntoView(btn: HTMLElement, behavior: ScrollBehavior): void {
        const nav = this.nav;
        if (!nav || nav.scrollWidth <= nav.clientWidth) return;
        const navRect = nav.getBoundingClientRect();
        const btnRect = btn.getBoundingClientRect();
        // Keep a little of the neighbouring tab visible past the edge fade
        const margin = 40;
        let left = nav.scrollLeft;
        if (btnRect.left < navRect.left + margin) {
            left += btnRect.left - navRect.left - margin;
        } else if (btnRect.right > navRect.right - margin) {
            left += btnRect.right - navRect.right + margin;
        } else {
            return;
        }
        nav.scrollTo({ left, behavior });
    }

    private restoreActiveTab(): void {
        let targetId: string | null = null;
        try {
            targetId = sessionStorage.getItem(this.storageKey);
        } catch { /* ignore unavailable sessionStorage (private browsing, etc.) */ }
        if (!targetId) return;

        const btn = this.nav?.querySelector<HTMLElement>(`[data-bs-target="${CSS.escape(targetId)}"]`);
        if (btn && !btn.classList.contains('active')) {
            // Bootstrap's Tab data-API is attached globally (conn_t3kit_base's
            // main.js does `import 'bootstrap'` once for the whole page) and
            // listens for real click events — conn_t3kit_elements has no direct
            // access to the Tab class itself, so a synthesized click is the
            // simplest way to trigger it with full ARIA/pane sync included.
            btn.click();
        }
    }

    private persistActiveTab(activeBtn: HTMLElement): void {
        const target = activeBtn.getAttribute('data-bs-target');
        if (!target) return;
        try {
            sessionStorage.setItem(this.storageKey, target);
        } catch { /* ignore unavailable sessionStorage */ }
    }

    private initIndicator(): void {
        const indicator = document.createElement('span');
        indicator.className = 'conn-tabs__indicator';
        this.nav!.appendChild(indicator);
        this.indicator = indicator;

        const activeBtn = this.nav!.querySelector<HTMLElement>(Tabs.ACTIVE_TAB_SEL);
        if (activeBtn) {
            // Position instantly on load — no slide-in from a zero/auto state.
            indicator.style.transitionDuration = '0s';
            this.moveIndicator(activeBtn);
            requestAnimationFrame(() => { indicator.style.transitionDuration = ''; });
        }
    }

    private moveIndicator(activeBtn: HTMLElement): void {
        if (!this.indicator || !this.nav) return;
        const navRect = this.nav.getBoundingClientRect();
        const btnRect = activeBtn.getBoundingClientRect();
        this.indicator.style.width     = `${btnRect.width}px`;
        // Indicator lives inside the (possibly scrolled) nav, so offset by scrollLeft
        this.indicator.style.transform = `translateX(${btnRect.left - navRect.left + this.nav.scrollLeft}px)`;
    }

}

import { registry } from '../Shared/registry';
registry.register('[id^="conn-tabs-"]', el => new Tabs(el));
