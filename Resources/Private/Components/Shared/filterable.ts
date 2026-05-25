/**
 * Shared base for Dashboard and Wiki.
 * Owns category / search / tag filter state and the DOM elements that drive it.
 * Subclasses implement applyFilters() and may override isDirty() / reset().
 */

export abstract class FilterableComponent {
    protected static readonly CATEGORY_ITEM_SEL  = '.conn-filter__category-item';
    protected static readonly CATEGORY_LABEL_SEL = '.conn-filter__category-label';
    protected static readonly SEARCH_SEL         = '.conn-filter__search';
    protected static readonly RESET_BTN_SEL      = '.conn-filter__reset';
    protected static readonly TAG_BADGE_SEL      = '.conn-filter__tag-badge';
    protected static readonly CLASS_ACTIVE       = 'active';
    protected static readonly CLASS_IS_ACTIVE    = 'is-active';
    protected static readonly CLASS_D_NONE       = 'd-none';
    protected static readonly FILTER_ALL         = 'all';

    protected readonly categoryItems: NodeListOf<HTMLElement>;
    protected readonly categoryLabel: Element | null;
    protected readonly tagBadges: NodeListOf<HTMLElement>;
    protected readonly searchInput: HTMLInputElement | null;
    protected readonly resetBtn: Element | null;

    protected currentCategory = FilterableComponent.FILTER_ALL;
    protected currentSearch   = '';
    protected selectedTags    = new Set<string>();

    constructor(protected readonly container: HTMLElement) {
        this.categoryItems = container.querySelectorAll<HTMLElement>(FilterableComponent.CATEGORY_ITEM_SEL);
        this.categoryLabel = container.querySelector(FilterableComponent.CATEGORY_LABEL_SEL);
        this.tagBadges     = container.querySelectorAll<HTMLElement>(FilterableComponent.TAG_BADGE_SEL);
        this.searchInput   = container.querySelector<HTMLInputElement>(FilterableComponent.SEARCH_SEL);
        this.resetBtn      = container.querySelector(FilterableComponent.RESET_BTN_SEL);
    }

    protected setCategoryLabel(item: HTMLElement): void {
        if (!this.categoryLabel) return;
        const label = this.categoryLabel as HTMLElement;
        label.textContent = item.dataset.category === FilterableComponent.FILTER_ALL
            ? (label.dataset.default || item.textContent?.trim() || '')
            : item.textContent?.trim() || '';
    }

    protected matchesTags(item: HTMLElement): boolean {
        if (this.selectedTags.size === 0) return true;
        const itemTags = (item.dataset.tags || '').split(',').filter(Boolean);
        return [...this.selectedTags].every(t => itemTags.includes(t));
    }

    protected isDirty(): boolean {
        return this.currentCategory !== FilterableComponent.FILTER_ALL
            || this.currentSearch.trim() !== ''
            || this.selectedTags.size > 0;
    }

    protected syncResetBtn(): void {
        if (!this.resetBtn) return;
        (this.resetBtn as HTMLElement).hidden = !this.isDirty();
    }

    protected abstract applyFilters(): void;

    protected reset(): void {
        this.currentCategory = FilterableComponent.FILTER_ALL;
        this.currentSearch   = '';
        this.selectedTags.clear();

        this.categoryItems.forEach(i => i.classList.remove(FilterableComponent.CLASS_ACTIVE));
        const allCatItem = [...this.categoryItems].find(i => i.dataset.category === FilterableComponent.FILTER_ALL);
        if (allCatItem) {
            allCatItem.classList.add(FilterableComponent.CLASS_ACTIVE);
            this.setCategoryLabel(allCatItem);
        }

        if (this.searchInput) this.searchInput.value = '';
        this.tagBadges.forEach(b => b.classList.remove(FilterableComponent.CLASS_IS_ACTIVE));

        this.syncResetBtn();
        this.applyFilters();
    }

    protected initFilterListeners(): void {
        const F = FilterableComponent;

        this.categoryItems.forEach(item => {
            item.addEventListener('click', (e: MouseEvent) => {
                e.preventDefault();
                this.categoryItems.forEach(i => i.classList.remove(F.CLASS_ACTIVE));
                item.classList.add(F.CLASS_ACTIVE);
                this.currentCategory = item.dataset.category ?? F.FILTER_ALL;
                this.setCategoryLabel(item);
                this.syncResetBtn();
                this.applyFilters();
            });
        });

        this.tagBadges.forEach(badge => {
            badge.addEventListener('click', () => {
                const tag = (badge.dataset.tag ?? '').toLowerCase();
                if (this.selectedTags.has(tag)) {
                    this.selectedTags.delete(tag);
                    badge.classList.remove(F.CLASS_IS_ACTIVE);
                } else {
                    this.selectedTags.add(tag);
                    badge.classList.add(F.CLASS_IS_ACTIVE);
                }
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

        if (this.resetBtn) {
            this.resetBtn.addEventListener('click', () => this.reset());
        }
    }
}
