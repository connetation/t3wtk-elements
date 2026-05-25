/**
 * Connetation Elements – Dashboard content element
 */

import { FilterController } from '../Shared/filter-controller';
import { registry } from '../Shared/registry';

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

    private static readonly FILTER_ITEM_SEL  = '.dashboard__filter-item';
    private static readonly FILTER_LABEL_SEL = '.dashboard__filter-label';
    private static readonly VIEW_BTN_SEL     = '.dashboard__view-btn';
    private static readonly STAT_CARD_SEL    = '.dashboard__stat[data-status]';
    private static readonly PROJECT_ITEM_SEL = '.dashboard__project-item';
    private static readonly EMPTY_SEL        = '.dashboard__empty';
    private static readonly CLASS_EMPTY      = 'dashboard__empty';
    private static readonly VISIT_LINK_SEL   = '.dashboard__visit-link';
    private static readonly VIEW_GRID        = 'grid';
    private static readonly VIEW_LIST        = 'list';

    private readonly uid: string;
    private readonly grid: HTMLElement | null;
    private readonly list: HTMLElement | null;
    private readonly modal: HTMLElement | null;
    private readonly modalTitle: HTMLElement | null;
    private readonly modalBody: HTMLElement | null;
    private readonly filterItems: NodeListOf<HTMLElement>;
    private readonly filterLabel: Element | null;
    private readonly viewBtns: NodeListOf<HTMLElement>;
    private readonly statCards: NodeListOf<HTMLElement>;
    private readonly emptyEl: Element | null;
    private readonly filter: FilterController;

    private currentFilter = FilterController.FILTER_ALL;

    constructor(private readonly container: HTMLElement) {
        this.uid        = container.id.replace('dashboard-', '').replace('-container', '');
        this.grid       = document.getElementById('dashboard-' + this.uid + '-grid');
        this.list       = document.getElementById('dashboard-' + this.uid + '-list');
        this.modal      = document.getElementById('projectModal-' + this.uid);
        this.modalTitle = document.getElementById('projectModalLabel-' + this.uid);
        this.modalBody  = document.getElementById('projectModalBody-' + this.uid);
        this.filterItems = container.querySelectorAll<HTMLElement>(Dashboard.FILTER_ITEM_SEL);
        this.filterLabel = container.querySelector(Dashboard.FILTER_LABEL_SEL);
        this.viewBtns    = container.querySelectorAll<HTMLElement>(Dashboard.VIEW_BTN_SEL);
        this.statCards   = container.querySelectorAll<HTMLElement>(Dashboard.STAT_CARD_SEL);
        this.emptyEl     = this.grid ? this.grid.querySelector(Dashboard.EMPTY_SEL) : null;

        this.filter = new FilterController(container, {
            onFilter:   () => this.applyFilters(),
            extraDirty: () => this.currentFilter !== FilterController.FILTER_ALL,
            onReset:    () => this.resetStatusFilter(),
        });

        this.init();
    }

    private setStatusLabel(item: HTMLElement): void {
        if (!this.filterLabel) return;
        const label = this.filterLabel as HTMLElement;
        label.textContent = item.dataset.status === FilterController.FILTER_ALL
            ? (label.dataset.default || item.textContent?.trim() || '')
            : item.textContent?.trim() || '';
    }

    // ─── Central filter setter ──────────────────────────────────────────────
    private setFilter(status: string): void {
        this.currentFilter = status;

        this.filterItems.forEach(i => i.classList.remove(FilterController.CLASS_ACTIVE));
        const activeItem = [...this.filterItems].find(i => i.dataset.status === status);
        if (activeItem) {
            activeItem.classList.add(FilterController.CLASS_ACTIVE);
            this.setStatusLabel(activeItem);
        }

        this.statCards.forEach(c => c.classList.remove(FilterController.CLASS_IS_ACTIVE));
        const activeCard = [...this.statCards].find(c => c.dataset.status === status);
        if (activeCard) activeCard.classList.add(FilterController.CLASS_IS_ACTIVE);

        this.filter.syncResetBtn();
        this.applyFilters();
    }

    // ─── Resets status-specific UI; called by FilterController's onReset hook ─
    private resetStatusFilter(): void {
        this.currentFilter = FilterController.FILTER_ALL;

        this.filterItems.forEach(i => i.classList.remove(FilterController.CLASS_ACTIVE));
        const allStatusItem = [...this.filterItems].find(i => i.dataset.status === FilterController.FILTER_ALL);
        if (allStatusItem) {
            allStatusItem.classList.add(FilterController.CLASS_ACTIVE);
            this.setStatusLabel(allStatusItem);
        }

        this.statCards.forEach(c => c.classList.remove(FilterController.CLASS_IS_ACTIVE));
        const totalCard = [...this.statCards].find(c => c.dataset.status === FilterController.FILTER_ALL);
        if (totalCard) totalCard.classList.add(FilterController.CLASS_IS_ACTIVE);
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

        const toggle = (status: string, count: number): void => {
            const card = this.container.querySelector(`.dashboard__stat[data-status="${status}"]`);
            if (card) card.classList.toggle(FilterController.CLASS_D_NONE, count === 0);
            const filterItem = this.container.querySelector(`.dashboard__filter-item[data-status="${status}"]`);
            if (filterItem) filterItem.classList.toggle(FilterController.CLASS_D_NONE, count === 0);
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
        const q = this.filter.currentSearch.toLowerCase().trim();
        const F = FilterController;
        let visibleGrid = 0;

        if (this.grid) {
            this.grid.querySelectorAll<HTMLElement>(Dashboard.PROJECT_ITEM_SEL).forEach(item => {
                if (item.classList.contains(Dashboard.CLASS_EMPTY)) return;
                const statusOk   = this.currentFilter === F.FILTER_ALL || item.dataset.status === this.currentFilter;
                const categoryOk = this.filter.currentCategory === F.FILTER_ALL || item.dataset.category === this.filter.currentCategory;
                const searchOk   = !q || (item.dataset.search || '').toLowerCase().includes(q);
                const show = statusOk && categoryOk && searchOk && this.filter.matchesTags(item);
                item.classList.toggle(F.CLASS_D_NONE, !show);
                if (show) visibleGrid++;
            });
        }

        if (this.list) {
            this.list.querySelectorAll<HTMLElement>(Dashboard.PROJECT_ITEM_SEL).forEach(item => {
                const statusOk   = this.currentFilter === F.FILTER_ALL || item.dataset.status === this.currentFilter;
                const categoryOk = this.filter.currentCategory === F.FILTER_ALL || item.dataset.category === this.filter.currentCategory;
                const searchOk   = !q || (item.dataset.search || '').toLowerCase().includes(q);
                item.classList.toggle(F.CLASS_D_NONE, !(statusOk && categoryOk && searchOk && this.filter.matchesTags(item)));
            });
        }

        if (this.emptyEl) this.emptyEl.classList.toggle(F.CLASS_D_NONE, visibleGrid > 0);
    }

    private init(): void {
        const F = FilterController;

        // ─── Status filter items ──────────────────────────────────────────────
        this.filterItems.forEach(item => {
            item.addEventListener('click', (e: MouseEvent) => {
                e.preventDefault();
                this.setFilter(item.dataset.status ?? F.FILTER_ALL);
            });
        });

        // ─── Stat cards ───────────────────────────────────────────────────────
        this.statCards.forEach(card => {
            const activate = (): void => {
                const status = card.dataset.status ?? F.FILTER_ALL;
                this.setFilter(card.classList.contains(F.CLASS_IS_ACTIVE) ? F.FILTER_ALL : status);
            };
            card.addEventListener('click', activate);
            card.addEventListener('keydown', (e: KeyboardEvent) => {
                if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); activate(); }
            });
        });

        // ─── View toggle ──────────────────────────────────────────────────────
        this.viewBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                this.viewBtns.forEach(b => b.classList.remove(F.CLASS_ACTIVE));
                btn.classList.add(F.CLASS_ACTIVE);
                const view = btn.dataset.view;
                if (this.grid) this.grid.classList.toggle(F.CLASS_D_NONE, view !== Dashboard.VIEW_GRID);
                if (this.list) this.list.classList.toggle(F.CLASS_D_NONE, view !== Dashboard.VIEW_LIST);
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

        // ─── Init state ───────────────────────────────────────────────────────
        this.updateStats();
        this.setFilter(F.FILTER_ALL);

        const defaultView = this.container.dataset.defaultView || Dashboard.VIEW_GRID;
        this.viewBtns.forEach(btn => btn.classList.toggle(F.CLASS_ACTIVE, btn.dataset.view === defaultView));
        if (this.grid) this.grid.classList.toggle(F.CLASS_D_NONE, defaultView !== Dashboard.VIEW_GRID);
        if (this.list) this.list.classList.toggle(F.CLASS_D_NONE, defaultView !== Dashboard.VIEW_LIST);
    }

}

export function initDashboard(container: HTMLElement): void {
    new Dashboard(container);
}

registry.register('.dashboard', el => new Dashboard(el));
