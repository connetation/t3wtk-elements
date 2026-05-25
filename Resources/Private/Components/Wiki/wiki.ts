/**
 * Connetation Elements – Wiki content element
 */

class Wiki {
    private static readonly CONTAINER_SEL        = '.wiki';
    private static readonly NAV_ITEM_SEL         = '.wiki__nav-item';
    private static readonly PANEL_SEL            = '.wiki__page-panel';
    private static readonly SEARCH_SEL           = '.conn-filter__search';
    private static readonly CATEGORY_ITEM_SEL    = '.conn-filter__category-item';
    private static readonly CATEGORY_LABEL_SEL   = '.conn-filter__category-label';
    private static readonly TAG_BADGE_SEL        = '.conn-filter__tag-badge';
    private static readonly RESET_BTN_SEL        = '.conn-filter__reset';
    private static readonly NAV_EMPTY_SEL        = '.wiki__nav-empty';
    private static readonly NAV_BTN_SEL          = '.wiki__nav-btn';
    private static readonly CODE_BLOCK_PRE_SEL   = '.wiki__block--code pre';
    private static readonly CODE_WRAP_SEL        = '.wiki__code-wrap';
    private static readonly COPY_BTN_SEL         = '.wiki__copy-btn';
    private static readonly CLASS_IS_ACTIVE      = 'is-active';
    private static readonly CLASS_ACTIVE         = 'active';
    private static readonly CLASS_D_NONE         = 'd-none';
    private static readonly CLASS_IS_COPIED      = 'is-copied';
    private static readonly CODE_HEADER_CLASS    = 'wiki__code-header';
    private static readonly CODE_LANG_CLASS      = 'wiki__code-lang';
    private static readonly COPY_BTN_CLASS       = 'wiki__copy-btn';
    private static readonly FILTER_ALL           = 'all';

    private readonly navItems: NodeListOf<HTMLElement>;
    private readonly panels: NodeListOf<HTMLElement>;
    private readonly searchInput: HTMLInputElement | null;
    private readonly categoryItems: NodeListOf<HTMLElement>;
    private readonly categoryLabel: Element | null;
    private readonly tagBadges: NodeListOf<HTMLElement>;
    private readonly resetBtn: Element | null;
    private readonly navEmpty: Element | null;
    private readonly panelText: Map<string | undefined, string>;

    private currentCategory = Wiki.FILTER_ALL;
    private currentSearch   = '';
    private selectedTags    = new Set<string>();

    constructor(private readonly container: HTMLElement) {
        this.navItems      = container.querySelectorAll<HTMLElement>(Wiki.NAV_ITEM_SEL);
        this.panels        = container.querySelectorAll<HTMLElement>(Wiki.PANEL_SEL);
        this.searchInput   = container.querySelector(Wiki.SEARCH_SEL) as HTMLInputElement | null;
        this.categoryItems = container.querySelectorAll<HTMLElement>(Wiki.CATEGORY_ITEM_SEL);
        this.categoryLabel = container.querySelector(Wiki.CATEGORY_LABEL_SEL);
        this.tagBadges     = container.querySelectorAll<HTMLElement>(Wiki.TAG_BADGE_SEL);
        this.resetBtn      = container.querySelector(Wiki.RESET_BTN_SEL);
        this.navEmpty      = container.querySelector(Wiki.NAV_EMPTY_SEL);

        // ─── Precompute full text of each page panel for search ─────────────────
        this.panelText = new Map<string | undefined, string>();
        this.panels.forEach(panel => {
            this.panelText.set(panel.dataset.pageUid, panel.textContent?.toLowerCase() ?? '');
        });

        this.init();
    }

    private setCategoryLabel(item: HTMLElement): void {
        if (!this.categoryLabel) return;
        const label = this.categoryLabel as HTMLElement;
        label.textContent = item.dataset.category === Wiki.FILTER_ALL
            ? (label.dataset.default || item.textContent.trim())
            : item.textContent.trim();
    }

    // ─── Show a specific page panel ─────────────────────────────────────────
    private showPage(pageUid: string | undefined): void {
        this.panels.forEach(p => {
            const active = p.dataset.pageUid === pageUid;
            p.classList.toggle(Wiki.CLASS_IS_ACTIVE, active);
            p.hidden = !active;
            p.setAttribute('aria-hidden', String(!active));
        });
        this.navItems.forEach(i => i.classList.toggle(Wiki.CLASS_IS_ACTIVE, i.dataset.pageUid === pageUid));
    }

    // ─── Show/hide reset button ──────────────────────────────────────────────
    private updateResetBtn(): void {
        if (!this.resetBtn) return;
        const dirty = this.currentCategory !== Wiki.FILTER_ALL || this.selectedTags.size > 0 || this.currentSearch.trim() !== '';
        (this.resetBtn as HTMLElement).hidden = !dirty;
    }

    // ─── Apply all filters ───────────────────────────────────────────────────
    private applyFilter(): void {
        const q = this.currentSearch.toLowerCase().trim();
        let firstVisible: string | undefined = undefined;

        this.navItems.forEach(item => {
            const catOk    = this.currentCategory === Wiki.FILTER_ALL || item.dataset.category === this.currentCategory;
            const searchOk = !q || (this.panelText.get(item.dataset.pageUid) || '').includes(q);

            let tagOk = true;
            if (this.selectedTags.size > 0) {
                const pageTags = item.dataset.tags ? item.dataset.tags.split(',') : [];
                tagOk = [...this.selectedTags].every(tag => pageTags.includes(tag));
            }

            const visible = catOk && searchOk && tagOk;
            item.classList.toggle(Wiki.CLASS_D_NONE, !visible);
            if (visible && !firstVisible) firstVisible = item.dataset.pageUid;
        });

        if (this.navEmpty) this.navEmpty.classList.toggle(Wiki.CLASS_D_NONE, firstVisible !== undefined);
        if (firstVisible) this.showPage(firstVisible);
        this.updateResetBtn();
    }

    // ─── Reset all filters ───────────────────────────────────────────────────
    private resetFilters(): void {
        this.currentCategory = Wiki.FILTER_ALL;
        this.currentSearch   = '';
        this.selectedTags.clear();

        if (this.searchInput) this.searchInput.value = '';

        this.categoryItems.forEach(i => i.classList.remove(Wiki.CLASS_ACTIVE));
        const allItem = [...this.categoryItems].find(i => i.dataset.category === Wiki.FILTER_ALL);
        if (allItem) {
            allItem.classList.add(Wiki.CLASS_ACTIVE);
            this.setCategoryLabel(allItem);
        }

        this.tagBadges.forEach(b => b.classList.remove(Wiki.CLASS_IS_ACTIVE));

        this.applyFilter();
    }

    private init(): void {
        // ─── Nav item click ──────────────────────────────────────────────────────
        this.navItems.forEach(item => {
            item.querySelector(Wiki.NAV_BTN_SEL)?.addEventListener('click', () => {
                this.showPage(item.dataset.pageUid);
            });
        });

        // ─── Category dropdown click ─────────────────────────────────────────────
        this.categoryItems.forEach(item => {
            item.addEventListener('click', (e: MouseEvent) => {
                e.preventDefault();
                this.categoryItems.forEach(i => i.classList.remove(Wiki.CLASS_ACTIVE));
                item.classList.add(Wiki.CLASS_ACTIVE);
                this.currentCategory = item.dataset.category ?? Wiki.FILTER_ALL;
                this.setCategoryLabel(item);
                this.updateResetBtn();
                this.applyFilter();
            });
        });

        // ─── Tag badge click (toggle, multi-select) ──────────────────────────────
        this.tagBadges.forEach(badge => {
            badge.addEventListener('click', () => {
                const tagName = (badge.dataset.tag ?? '').toLowerCase();
                if (this.selectedTags.has(tagName)) {
                    this.selectedTags.delete(tagName);
                    badge.classList.remove(Wiki.CLASS_IS_ACTIVE);
                } else {
                    this.selectedTags.add(tagName);
                    badge.classList.add(Wiki.CLASS_IS_ACTIVE);
                }
                this.applyFilter();
            });
        });

        // ─── Search input ────────────────────────────────────────────────────────
        if (this.searchInput) {
            this.searchInput.addEventListener('input', () => {
                this.currentSearch = this.searchInput!.value;
                this.applyFilter();
            });
        }

        // ─── Reset button ────────────────────────────────────────────────────────
        if (this.resetBtn) {
            this.resetBtn.addEventListener('click', () => this.resetFilters());
        }

        // ─── Inject copy buttons into CKEditor code blocks ──────────────────────
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

        // ─── Init: show first page ───────────────────────────────────────────────
        if (this.navItems.length) this.showPage(this.navItems[0].dataset.pageUid);
    }

    static initAll(): void {
        document.querySelectorAll<HTMLElement>(Wiki.CONTAINER_SEL)
            .forEach(el => new Wiki(el));
    }
}

document.addEventListener('DOMContentLoaded', () => Wiki.initAll());
