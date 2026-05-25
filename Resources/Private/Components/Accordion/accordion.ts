/**
 * Conn Bootstrap Accordion
 * Core open/close behaviour is handled natively by Bootstrap 5 via
 * data-bs-toggle / data-bs-target attributes — no extra code needed.
 *
 * This module adds optional session-state persistence: panels that were
 * open when the user navigates away will still be open on return.
 * State persistence is skipped for mutual-exclusion accordions
 * (those using data-bs-parent), because only one panel is ever open.
 */

class Accordion {

    private static readonly MUTUAL_EXCL_SEL     = '[data-bs-parent]';
    private static readonly CLASS_SHOW          = 'show';
    private static readonly CLASS_COLLAPSED     = 'collapsed';
    private static readonly EVENT_SHOWN         = 'shown.bs.collapse';
    private static readonly EVENT_HIDDEN        = 'hidden.bs.collapse';
    private static readonly STORAGE_KEY_PREFIX  = 'conn-acc:';
    private static readonly OPEN_PANELS_SEL     = '.accordion-collapse.show';

    private storageKey = '';

    constructor(private readonly el: HTMLElement) {
        const hasMutualExclusion = el.querySelector(Accordion.MUTUAL_EXCL_SEL) !== null;
        if (hasMutualExclusion) return;

        this.storageKey = `${Accordion.STORAGE_KEY_PREFIX}${el.id}`;
        this.init();
    }

    private init(): void {
        try {
            const saved = JSON.parse(sessionStorage.getItem(this.storageKey) ?? '[]') as string[];
            saved.forEach(panelId => {
                const panel = this.el.querySelector(`#${panelId}`);
                const btn   = this.el.querySelector(`[data-bs-target="#${panelId}"]`);
                if (panel && !panel.classList.contains(Accordion.CLASS_SHOW)) {
                    panel.classList.add(Accordion.CLASS_SHOW);
                    btn?.classList.remove(Accordion.CLASS_COLLAPSED);
                    btn?.setAttribute('aria-expanded', 'true');
                }
            });
        } catch { /* ignore malformed sessionStorage data */ }

        this.el.addEventListener(Accordion.EVENT_SHOWN,  () => this.persistState());
        this.el.addEventListener(Accordion.EVENT_HIDDEN, () => this.persistState());
    }

    private persistState(): void {
        const openIds = [...this.el.querySelectorAll(Accordion.OPEN_PANELS_SEL)]
            .map(el => el.id);
        sessionStorage.setItem(this.storageKey, JSON.stringify(openIds));
    }

}

import { registry } from '../Shared/registry';
registry.register('[id^="conn-accordion-"]', el => new Accordion(el));
