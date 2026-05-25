/**
 * Connetation Elements – Wiki content element
 */

import { FilterController } from '../Shared/filter-controller';
import { registry } from '../Shared/registry';

class Wiki {

    private static readonly NAV_ITEM_SEL       = '.wiki__nav-item';
    private static readonly PANEL_SEL          = '.wiki__page-panel';
    private static readonly NAV_EMPTY_SEL      = '.wiki__nav-empty';
    private static readonly NAV_BTN_SEL        = '.wiki__nav-btn';
    private static readonly CODE_BLOCK_PRE_SEL = '.wiki__block--code pre';
    private static readonly CODE_WRAP_SEL      = '.wiki__code-wrap';
    private static readonly COPY_BTN_SEL       = '.wiki__copy-btn';
    private static readonly CLASS_IS_COPIED    = 'is-copied';
    private static readonly CODE_HEADER_CLASS  = 'wiki__code-header';
    private static readonly CODE_LANG_CLASS    = 'wiki__code-lang';
    private static readonly COPY_BTN_CLASS     = 'wiki__copy-btn';

    private readonly navItems: NodeListOf<HTMLElement>;
    private readonly panels: NodeListOf<HTMLElement>;
    private readonly navEmpty: Element | null;
    private readonly panelText: Map<string | undefined, string>;
    private readonly filter: FilterController;

    constructor(private readonly container: HTMLElement) {
        this.navItems  = container.querySelectorAll<HTMLElement>(Wiki.NAV_ITEM_SEL);
        this.panels    = container.querySelectorAll<HTMLElement>(Wiki.PANEL_SEL);
        this.navEmpty  = container.querySelector(Wiki.NAV_EMPTY_SEL);

        // Precompute full text of each page panel for search
        this.panelText = new Map<string | undefined, string>();
        this.panels.forEach(panel => {
            this.panelText.set(panel.dataset.pageUid, panel.textContent?.toLowerCase() ?? '');
        });

        this.filter = new FilterController(container, {
            onFilter: () => this.applyFilters(),
        });

        this.init();
    }

    // ─── Show a specific page panel ─────────────────────────────────────────
    private showPage(pageUid: string | undefined): void {
        const F = FilterController;
        this.panels.forEach(p => {
            const active = p.dataset.pageUid === pageUid;
            p.classList.toggle(F.CLASS_IS_ACTIVE, active);
            p.hidden = !active;
            p.setAttribute('aria-hidden', String(!active));
        });
        this.navItems.forEach(i => i.classList.toggle(F.CLASS_IS_ACTIVE, i.dataset.pageUid === pageUid));
    }

    // ─── Apply all filters ───────────────────────────────────────────────────
    private applyFilters(): void {
        const q = this.filter.currentSearch.toLowerCase().trim();
        const F = FilterController;
        let firstVisible: string | undefined = undefined;

        this.navItems.forEach(item => {
            const catOk    = this.filter.currentCategory === F.FILTER_ALL || item.dataset.category === this.filter.currentCategory;
            const searchOk = !q || (this.panelText.get(item.dataset.pageUid) || '').includes(q);
            const visible  = catOk && searchOk && this.filter.matchesTags(item);
            item.classList.toggle(F.CLASS_D_NONE, !visible);
            if (visible && !firstVisible) firstVisible = item.dataset.pageUid;
        });

        if (this.navEmpty) this.navEmpty.classList.toggle(F.CLASS_D_NONE, firstVisible !== undefined);
        if (firstVisible) this.showPage(firstVisible);
    }

    private init(): void {
        // ─── Nav item click ──────────────────────────────────────────────────
        this.navItems.forEach(item => {
            item.querySelector(Wiki.NAV_BTN_SEL)?.addEventListener('click', () => {
                this.showPage(item.dataset.pageUid);
            });
        });

        // ─── Inject copy buttons into CKEditor code blocks ───────────────────
        const ICON_COPY  = `<svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true"><path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/><path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h3zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z"/></svg>`;
        const ICON_CHECK = `<svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true"><path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/></svg>`;

        this.container.querySelectorAll(Wiki.CODE_BLOCK_PRE_SEL).forEach(pre => {
            const wrap = pre.closest(Wiki.CODE_WRAP_SEL);
            if (!wrap || wrap.querySelector(Wiki.COPY_BTN_SEL)) return;

            const lang = pre.querySelector('code')?.className.replace('language-', '') || 'code';

            const header = document.createElement('div');
            header.className = Wiki.CODE_HEADER_CLASS;
            header.innerHTML = `<span class="${Wiki.CODE_LANG_CLASS}">${lang}</span>
            <button type="button" class="${Wiki.COPY_BTN_CLASS}" aria-label="Copy code">${ICON_COPY} Copy</button>`;
            wrap.insertBefore(header, pre);

            header.querySelector(Wiki.COPY_BTN_SEL)?.addEventListener('click', () => {
                const text = pre.querySelector('code')?.textContent ?? '';
                navigator.clipboard?.writeText(text).then(() => {
                    const btn = header.querySelector<HTMLButtonElement>(Wiki.COPY_BTN_SEL);
                    if (!btn) return;
                    btn.innerHTML = `${ICON_CHECK} Copied!`;
                    btn.classList.add(Wiki.CLASS_IS_COPIED);
                    setTimeout(() => {
                        btn.innerHTML = `${ICON_COPY} Copy`;
                        btn.classList.remove(Wiki.CLASS_IS_COPIED);
                    }, 1500);
                });
            });
        });

        // ─── Show first page on load ─────────────────────────────────────────
        if (this.navItems.length) this.showPage(this.navItems[0].dataset.pageUid);
    }

}

registry.register('.wiki', el => new Wiki(el));
