/**
 * Conn Bootstrap Tabs
 * Core show/hide behaviour is handled natively by Bootstrap 5 via
 * data-bs-toggle / data-bs-target attributes — no extra code needed.
 *
 * This module adds two enhancements:
 *  - a sliding active-indicator bar under the nav-tabs style (skipped for
 *    the nav-pills style, which already has its own solid active background)
 *  - session-state persistence of the last active tab, so returning to the
 *    page (e.g. via browser back) keeps the previously selected pane open
 */

class Tabs {

    private static readonly EVENT_SHOWN        = 'shown.bs.tab';
    private static readonly STORAGE_KEY_PREFIX = 'conn-tabs:';
    private static readonly ACTIVE_TAB_SEL     = '.nav-link.active';

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

        if (this.nav?.classList.contains('nav-tabs')) {
            this.initIndicator();
        }

        this.el.addEventListener(Tabs.EVENT_SHOWN, (event) => {
            this.persistActiveTab(event.target as HTMLElement);
            this.moveIndicator(event.target as HTMLElement);
        });
    }

    private restoreActiveTab(): void {
        let targetId: string | null = null;
        try {
            targetId = sessionStorage.getItem(this.storageKey);
        } catch { /* ignore unavailable sessionStorage (private browsing, etc.) */ }
        if (!targetId) return;

        const btn = this.nav?.querySelector<HTMLElement>(`[data-bs-target="${CSS.escape(targetId)}"]`);
        if (btn && !btn.classList.contains('active')) {
            // Bootstrap's Tab data-API is attached globally (conn_base's
            // main.js does `import 'bootstrap'` once for the whole page) and
            // listens for real click events — conn_elements has no direct
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
        this.indicator.style.transform = `translateX(${btnRect.left - navRect.left}px)`;
    }

}

import { registry } from '../Shared/registry';
registry.register('[id^="conn-tabs-"]', el => new Tabs(el));
