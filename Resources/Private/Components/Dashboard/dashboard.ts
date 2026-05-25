/**
 * Connetation Elements – Dashboard content element
 */

interface DashboardStatCounts {
    total:       Element | null;
    active:      Element | null;
    in_progress: Element | null;
    coming_soon: Element | null;
    inactive:    Element | null;
    deprecated:  Element | null;
    archived:    Element | null;
}

export class Dashboard {
    private static readonly CONTAINER_SEL          = '.dashboard';
    private static readonly SEARCH_SEL             = '.conn-filter__search';
    private static readonly FILTER_ITEM_SEL        = '.dashboard__filter-item';
    private static readonly FILTER_LABEL_SEL       = '.dashboard__filter-label';
    private static readonly CATEGORY_ITEM_SEL      = '.conn-filter__category-item';
    private static readonly CATEGORY_LABEL_SEL     = '.conn-filter__category-label';
    private static readonly VIEW_BTN_SEL           = '.dashboard__view-btn';
    private static readonly STAT_CARD_SEL          = '.dashboard__stat[data-status]';
    private static readonly RESET_BTN_SEL          = '.conn-filter__reset';
    private static readonly TAG_BADGE_SEL          = '.conn-filter__tag-badge';
    private static readonly PROJECT_ITEM_SEL       = '.dashboard__project-item';
    private static readonly EMPTY_SEL              = '.dashboard__empty';
    private static readonly CLASS_EMPTY            = 'dashboard__empty';
    private static readonly VISIT_LINK_SEL         = '.dashboard__visit-link';
    private static readonly CLASS_ACTIVE           = 'active';
    private static readonly CLASS_IS_ACTIVE        = 'is-active';
    private static readonly CLASS_D_NONE           = 'd-none';
    private static readonly FILTER_ALL             = 'all';
    private static readonly VIEW_GRID              = 'grid';
    private static readonly VIEW_LIST              = 'list';

    private readonly uid: string;
    private readonly grid: HTMLElement | null;
    private readonly list: HTMLElement | null;
    private readonly modal: HTMLElement | null;
    private readonly modalTitle: HTMLElement | null;
    private readonly modalBody: HTMLElement | null;
    private readonly searchInput: HTMLInputElement | null;
    private readonly filterItems: NodeListOf<HTMLElement>;
    private readonly filterLabel: Element | null;
    private readonly categoryItems: NodeListOf<HTMLElement>;
    private readonly categoryLabel: Element | null;
    private readonly viewBtns: NodeListOf<HTMLElement>;
    private readonly statCards: NodeListOf<HTMLElement>;
    private readonly resetBtn: Element | null;
    private readonly tagBadges: NodeListOf<HTMLElement>;
    private readonly emptyEl: Element | null;

    private currentFilter   = Dashboard.FILTER_ALL;
    private currentCategory = Dashboard.FILTER_ALL;
    private currentSearch   = '';
    private selectedTags    = new Set<string>();

    constructor(private readonly container: HTMLElement) {
        this.uid           = container.id.replace('dashboard-', '').replace('-container', '');
        this.grid          = document.getElementById('dashboard-' + this.uid + '-grid');
        this.list          = document.getElementById('dashboard-' + this.uid + '-list');
        this.modal         = document.getElementById('projectModal-' + this.uid);
        this.modalTitle    = document.getElementById('projectModalLabel-' + this.uid);
        this.modalBody     = document.getElementById('projectModalBody-' + this.uid);
        this.searchInput   = container.querySelector(Dashboard.SEARCH_SEL) as HTMLInputElement | null;
        this.filterItems   = container.querySelectorAll<HTMLElement>(Dashboard.FILTER_ITEM_SEL);
        this.filterLabel   = container.querySelector(Dashboard.FILTER_LABEL_SEL);
        this.categoryItems = container.querySelectorAll<HTMLElement>(Dashboard.CATEGORY_ITEM_SEL);
        this.categoryLabel = container.querySelector(Dashboard.CATEGORY_LABEL_SEL);
        this.viewBtns      = container.querySelectorAll<HTMLElement>(Dashboard.VIEW_BTN_SEL);
        this.statCards     = container.querySelectorAll<HTMLElement>(Dashboard.STAT_CARD_SEL);
        this.resetBtn      = container.querySelector(Dashboard.RESET_BTN_SEL);
        this.tagBadges     = container.querySelectorAll<HTMLElement>(Dashboard.TAG_BADGE_SEL);
        this.emptyEl       = this.grid ? this.grid.querySelector(Dashboard.EMPTY_SEL) : null;

        this.init();
    }

    private setCategoryLabel(item: HTMLElement): void {
        if (!this.categoryLabel) return;
        const label = this.categoryLabel as HTMLElement;
        label.textContent = item.dataset.category === Dashboard.FILTER_ALL
            ? (label.dataset.default || item.textContent.trim())
            : item.textContent.trim();
    }

    private setStatusLabel(item: HTMLElement): void {
        if (!this.filterLabel) return;
        const label = this.filterLabel as HTMLElement;
        label.textContent = item.dataset.status === Dashboard.FILTER_ALL
            ? (label.dataset.default || item.textContent.trim())
            : item.textContent.trim();
    }

    // ─── Reset button visibility ────────────────────────────────────────────
    private syncResetBtn(): void {
        if (!this.resetBtn) return;
        const dirty = this.currentFilter !== Dashboard.FILTER_ALL || this.currentCategory !== Dashboard.FILTER_ALL || this.currentSearch !== '' || this.selectedTags.size > 0;
        (this.resetBtn as HTMLElement).hidden = !dirty;
    }

    // ─── Central filter setter ──────────────────────────────────────────────
    private setFilter(status: string): void {
        this.currentFilter = status;

        // Sync dropdown items + label
        this.filterItems.forEach(i => i.classList.remove(Dashboard.CLASS_ACTIVE));
        const activeItem = [...this.filterItems].find(i => i.dataset.status === status);
        if (activeItem) {
            activeItem.classList.add(Dashboard.CLASS_ACTIVE);
            this.setStatusLabel(activeItem);
        }

        // Sync stat cards
        this.statCards.forEach(c => c.classList.remove(Dashboard.CLASS_IS_ACTIVE));
        const activeCard = [...this.statCards].find(c => c.dataset.status === status);
        if (activeCard) activeCard.classList.add(Dashboard.CLASS_IS_ACTIVE);

        this.syncResetBtn();
        this.applyFilters();
    }

    // ─── Reset all filters ──────────────────────────────────────────────────
    private resetAll(): void {
        this.currentFilter   = Dashboard.FILTER_ALL;
        this.currentCategory = Dashboard.FILTER_ALL;
        this.currentSearch   = '';

        // Sync status UI
        this.filterItems.forEach(i => i.classList.remove(Dashboard.CLASS_ACTIVE));
        const allStatusItem = [...this.filterItems].find(i => i.dataset.status === Dashboard.FILTER_ALL);
        if (allStatusItem) {
            allStatusItem.classList.add(Dashboard.CLASS_ACTIVE);
            this.setStatusLabel(allStatusItem);
        }
        this.statCards.forEach(c => c.classList.remove(Dashboard.CLASS_IS_ACTIVE));
        const totalCard = [...this.statCards].find(c => c.dataset.status === Dashboard.FILTER_ALL);
        if (totalCard) totalCard.classList.add(Dashboard.CLASS_IS_ACTIVE);

        // Sync category UI
        this.categoryItems.forEach(i => i.classList.remove(Dashboard.CLASS_ACTIVE));
        const allCatItem = [...this.categoryItems].find(i => i.dataset.category === Dashboard.FILTER_ALL);
        if (allCatItem) {
            allCatItem.classList.add(Dashboard.CLASS_ACTIVE);
            this.setCategoryLabel(allCatItem);
        }

        // Sync search
        if (this.searchInput) this.searchInput.value = '';

        // Sync tags
        this.selectedTags.clear();
        this.tagBadges.forEach(b => b.classList.remove(Dashboard.CLASS_IS_ACTIVE));

        this.syncResetBtn();
        this.applyFilters();
    }

    // ─── Stats counter ──────────────────────────────────────────────────────
    private updateStats(): void {
        if (!this.grid) return;
        let total = 0, active = 0, inProgress = 0, comingSoon = 0, inactive = 0, deprecated = 0, archived = 0;
        this.grid.querySelectorAll<HTMLElement>(Dashboard.PROJECT_ITEM_SEL).forEach(item => {
            if (item.classList.contains(Dashboard.CLASS_EMPTY)) return;
            total++;
            const s = item.dataset.status;
            if (s === 'active')      active++;
            if (s === 'in_progress') inProgress++;
            if (s === 'coming_soon') comingSoon++;
            if (s === 'inactive')    inactive++;
            if (s === 'deprecated')  deprecated++;
            if (s === 'archived')    archived++;
        });
        const statEls: DashboardStatCounts = {
            total:       this.container.querySelector('[data-stat="total"]'),
            active:      this.container.querySelector('[data-stat="active"]'),
            in_progress: this.container.querySelector('[data-stat="in_progress"]'),
            coming_soon: this.container.querySelector('[data-stat="coming_soon"]'),
            inactive:    this.container.querySelector('[data-stat="inactive"]'),
            deprecated:  this.container.querySelector('[data-stat="deprecated"]'),
            archived:    this.container.querySelector('[data-stat="archived"]'),
        };
        if (statEls.total)       statEls.total.textContent       = String(total);
        if (statEls.active)      statEls.active.textContent      = String(active);
        if (statEls.in_progress) statEls.in_progress.textContent = String(inProgress);
        if (statEls.coming_soon) statEls.coming_soon.textContent = String(comingSoon);
        if (statEls.inactive)    statEls.inactive.textContent    = String(inactive);
        if (statEls.deprecated)  statEls.deprecated.textContent  = String(deprecated);
        if (statEls.archived)    statEls.archived.textContent    = String(archived);

        // Show stat cards and filter items only when projects with that status exist
        const toggle = (status: string, count: number): void => {
            const card = this.container.querySelector(`.dashboard__stat[data-status="${status}"]`);
            if (card) card.classList.toggle(Dashboard.CLASS_D_NONE, count === 0);
            const filterItem = this.container.querySelector(`.dashboard__filter-item[data-status="${status}"]`);
            if (filterItem) filterItem.classList.toggle(Dashboard.CLASS_D_NONE, count === 0);
        };
        toggle('active',      active);
        toggle('in_progress', inProgress);
        toggle('coming_soon', comingSoon);
        toggle('inactive',    inactive);
        toggle('deprecated',  deprecated);
        toggle('archived',    archived);
    }

    // ─── Filter / search application ────────────────────────────────────────
    private applyFilters(): void {
        const q = this.currentSearch.toLowerCase().trim();
        let visibleGrid = 0;

        const matchesTags = (item: HTMLElement): boolean => {
            if (this.selectedTags.size === 0) return true;
            const itemTags = (item.dataset.tags || '').split(',').filter(Boolean);
            return [...this.selectedTags].every(t => itemTags.includes(t));
        };

        if (this.grid) {
            this.grid.querySelectorAll<HTMLElement>(Dashboard.PROJECT_ITEM_SEL).forEach(item => {
                if (item.classList.contains(Dashboard.CLASS_EMPTY)) return;
                const statusOk   = this.currentFilter === Dashboard.FILTER_ALL || item.dataset.status === this.currentFilter;
                const categoryOk = this.currentCategory === Dashboard.FILTER_ALL || item.dataset.category === this.currentCategory;
                const searchOk   = !q || (item.dataset.search || '').toLowerCase().includes(q);
                const tagsOk     = matchesTags(item);
                const show = statusOk && categoryOk && searchOk && tagsOk;
                item.classList.toggle(Dashboard.CLASS_D_NONE, !show);
                if (show) visibleGrid++;
            });
        }

        if (this.list) {
            this.list.querySelectorAll<HTMLElement>(Dashboard.PROJECT_ITEM_SEL).forEach(item => {
                const statusOk   = this.currentFilter === Dashboard.FILTER_ALL || item.dataset.status === this.currentFilter;
                const categoryOk = this.currentCategory === Dashboard.FILTER_ALL || item.dataset.category === this.currentCategory;
                const searchOk   = !q || (item.dataset.search || '').toLowerCase().includes(q);
                const tagsOk     = matchesTags(item);
                item.classList.toggle(Dashboard.CLASS_D_NONE, !(statusOk && categoryOk && searchOk && tagsOk));
            });
        }

        if (this.emptyEl) this.emptyEl.classList.toggle(Dashboard.CLASS_D_NONE, visibleGrid > 0);
    }

    private init(): void {
        // ─── Event listeners ─────────────────────────────────────────────────
        this.filterItems.forEach(item => {
            item.addEventListener('click', (e: MouseEvent) => {
                e.preventDefault();
                this.setFilter(item.dataset.status ?? Dashboard.FILTER_ALL);
            });
        });

        this.statCards.forEach(card => {
            const activate = (): void => {
                const status = card.dataset.status ?? Dashboard.FILTER_ALL;
                // Toggle: clicking the active card resets to all
                this.setFilter(card.classList.contains(Dashboard.CLASS_IS_ACTIVE) ? Dashboard.FILTER_ALL : status);
            };
            card.addEventListener('click', activate);
            card.addEventListener('keydown', (e: KeyboardEvent) => {
                if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); activate(); }
            });
        });

        if (this.resetBtn) {
            this.resetBtn.addEventListener('click', () => this.resetAll());
        }

        this.categoryItems.forEach(item => {
            item.addEventListener('click', (e: MouseEvent) => {
                e.preventDefault();
                this.categoryItems.forEach(i => i.classList.remove(Dashboard.CLASS_ACTIVE));
                item.classList.add(Dashboard.CLASS_ACTIVE);
                this.currentCategory = item.dataset.category ?? Dashboard.FILTER_ALL;
                this.setCategoryLabel(item);
                this.syncResetBtn();
                this.applyFilters();
            });
        });

        if (this.searchInput) {
            this.searchInput.addEventListener('input', () => {
                this.currentSearch = this.searchInput!.value;
                this.syncResetBtn();
                this.applyFilters();
            });
        }

        this.tagBadges.forEach(badge => {
            badge.addEventListener('click', () => {
                const tag = (badge.dataset.tag ?? '').toLowerCase();
                if (this.selectedTags.has(tag)) {
                    this.selectedTags.delete(tag);
                    badge.classList.remove(Dashboard.CLASS_IS_ACTIVE);
                } else {
                    this.selectedTags.add(tag);
                    badge.classList.add(Dashboard.CLASS_IS_ACTIVE);
                }
                this.syncResetBtn();
                this.applyFilters();
            });
        });

        this.viewBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                this.viewBtns.forEach(b => b.classList.remove(Dashboard.CLASS_ACTIVE));
                btn.classList.add(Dashboard.CLASS_ACTIVE);
                const view = btn.dataset.view;
                if (this.grid) this.grid.classList.toggle(Dashboard.CLASS_D_NONE, view !== Dashboard.VIEW_GRID);
                if (this.list) this.list.classList.toggle(Dashboard.CLASS_D_NONE, view !== Dashboard.VIEW_LIST);
            });
        });

        this.container.querySelectorAll(Dashboard.VISIT_LINK_SEL).forEach(link => {
            link.addEventListener('click', (e: Event) => e.stopPropagation());
        });

        if (this.modal && this.modalBody) {
            this.modal.addEventListener('show.bs.modal', (e: Event) => {
                const trigger = (e as Event & { relatedTarget?: HTMLElement }).relatedTarget ?? null;
                if (!trigger) return;
                const detailId = trigger.getAttribute('data-project-detail');
                const title    = trigger.getAttribute('data-project-title') || '';
                if (this.modalTitle) this.modalTitle.textContent = title;
                if (detailId) {
                    const detailEl = document.getElementById(detailId);
                    if (detailEl) this.modalBody!.innerHTML = detailEl.innerHTML;
                }
            });
        }

        // ─── Init ────────────────────────────────────────────────────────────
        this.updateStats();
        this.setFilter(Dashboard.FILTER_ALL); // marks total card active, sets dropdown, applies filters

        const defaultView = this.container.dataset.defaultView || Dashboard.VIEW_GRID;
        this.viewBtns.forEach(btn => btn.classList.toggle(Dashboard.CLASS_ACTIVE, btn.dataset.view === defaultView));
        if (this.grid) this.grid.classList.toggle(Dashboard.CLASS_D_NONE, defaultView !== Dashboard.VIEW_GRID);
        if (this.list) this.list.classList.toggle(Dashboard.CLASS_D_NONE, defaultView !== Dashboard.VIEW_LIST);
    }

    static initAll(): void {
        document.querySelectorAll<HTMLElement>(Dashboard.CONTAINER_SEL)
            .forEach(el => new Dashboard(el));
    }
}

export function initDashboard(container: HTMLElement): void {
    new Dashboard(container);
}

document.addEventListener('DOMContentLoaded', () => Dashboard.initAll());
