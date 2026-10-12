/*
 * This file is part of the Connetation TYPO3 Web Toolkit - T3WTK.
 *
 * Copyright (c) Connetation Web Engineering GmbH
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Composable filter controller for category / search / tag filtering.
 * Constructed with a container element and a callbacks object — no inheritance required.
 */

export interface FilterControllerOptions {
    /** Called whenever filter state changes and the component should re-render. */
    onFilter: () => void;
    /** Return true if the host component has additional dirty state beyond the shared filters. */
    extraDirty?: () => boolean;
    /** Called during reset, before onFilter, so the host can clear its own extra state first. */
    onReset?: () => void;
}

export class FilterController {
    static readonly FILTER_ALL      = 'all';
    static readonly CLASS_ACTIVE    = 'active';
    static readonly CLASS_IS_ACTIVE = 'is-active';
    static readonly CLASS_D_NONE    = 'd-none';

    private static readonly CATEGORY_ITEM_SEL  = '.conn-filter__category-item';
    private static readonly CATEGORY_LABEL_SEL = '.conn-filter__category-label';
    private static readonly SEARCH_SEL         = '.conn-filter__search';
    private static readonly RESET_BTN_SEL      = '.conn-filter__reset';
    private static readonly TAG_BADGE_SEL      = '.conn-filter__tag-badge';

    private readonly categoryItems: NodeListOf<HTMLElement>;
    private readonly categoryLabel: Element | null;
    private readonly tagBadges: NodeListOf<HTMLElement>;
    private readonly searchInput: HTMLInputElement | null;
    private readonly resetBtn: Element | null;

    private _currentCategory = FilterController.FILTER_ALL;
    private _currentSearch   = '';
    private _selectedTags    = new Set<string>();

    get currentCategory(): string            { return this._currentCategory; }
    get currentSearch(): string              { return this._currentSearch; }
    get selectedTags(): ReadonlySet<string>  { return this._selectedTags; }

    constructor(
        container: HTMLElement,
        private readonly options: FilterControllerOptions,
    ) {
        this.categoryItems = container.querySelectorAll<HTMLElement>(FilterController.CATEGORY_ITEM_SEL);
        this.categoryLabel = container.querySelector(FilterController.CATEGORY_LABEL_SEL);
        this.tagBadges     = container.querySelectorAll<HTMLElement>(FilterController.TAG_BADGE_SEL);
        this.searchInput   = container.querySelector<HTMLInputElement>(FilterController.SEARCH_SEL);
        this.resetBtn      = container.querySelector(FilterController.RESET_BTN_SEL);
        this.initListeners();
    }

    matchesTags(item: HTMLElement): boolean {
        if (this._selectedTags.size === 0) return true;
        const itemTags = (item.dataset.tags || '').split(',').filter(Boolean);
        return [...this._selectedTags].every(t => itemTags.includes(t));
    }

    isDirty(): boolean {
        return this._currentCategory !== FilterController.FILTER_ALL
            || this._currentSearch.trim() !== ''
            || this._selectedTags.size > 0
            || (this.options.extraDirty?.() ?? false);
    }

    syncResetBtn(): void {
        if (!this.resetBtn) return;
        (this.resetBtn as HTMLElement).hidden = !this.isDirty();
    }

    private setCategoryLabel(item: HTMLElement): void {
        if (!this.categoryLabel) return;
        const label = this.categoryLabel as HTMLElement;
        label.textContent = item.dataset.category === FilterController.FILTER_ALL
            ? (label.dataset.default || item.textContent?.trim() || '')
            : item.textContent?.trim() || '';
    }

    private reset(): void {
        this._currentCategory = FilterController.FILTER_ALL;
        this._currentSearch   = '';
        this._selectedTags.clear();

        this.categoryItems.forEach(i => i.classList.remove(FilterController.CLASS_ACTIVE));
        const allCatItem = [...this.categoryItems].find(i => i.dataset.category === FilterController.FILTER_ALL);
        if (allCatItem) {
            allCatItem.classList.add(FilterController.CLASS_ACTIVE);
            this.setCategoryLabel(allCatItem);
        }

        if (this.searchInput) this.searchInput.value = '';
        this.tagBadges.forEach(b => b.classList.remove(FilterController.CLASS_IS_ACTIVE));

        this.options.onReset?.();
        this.syncResetBtn();
        this.options.onFilter();
    }

    private initListeners(): void {
        const F = FilterController;

        this.categoryItems.forEach(item => {
            item.addEventListener('click', (e: MouseEvent) => {
                e.preventDefault();
                this.categoryItems.forEach(i => i.classList.remove(F.CLASS_ACTIVE));
                item.classList.add(F.CLASS_ACTIVE);
                this._currentCategory = item.dataset.category ?? F.FILTER_ALL;
                this.setCategoryLabel(item);
                this.syncResetBtn();
                this.options.onFilter();
            });
        });

        this.tagBadges.forEach(badge => {
            badge.addEventListener('click', () => {
                const tag = (badge.dataset.tag ?? '').toLowerCase();
                if (this._selectedTags.has(tag)) {
                    this._selectedTags.delete(tag);
                    badge.classList.remove(F.CLASS_IS_ACTIVE);
                } else {
                    this._selectedTags.add(tag);
                    badge.classList.add(F.CLASS_IS_ACTIVE);
                }
                this.syncResetBtn();
                this.options.onFilter();
            });
        });

        if (this.searchInput) {
            this.searchInput.addEventListener('input', () => {
                this._currentSearch = this.searchInput!.value;
                this.syncResetBtn();
                this.options.onFilter();
            });
        }

        if (this.resetBtn) {
            this.resetBtn.addEventListener('click', () => this.reset());
        }
    }
}
