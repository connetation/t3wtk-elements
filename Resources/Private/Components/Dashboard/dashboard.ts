/**
 * Connetation Elements – Dashboard content element
 */

const initDashboard = (container: HTMLElement): void => {
    const uid = container.id.replace('dashboard-', '').replace('-container', '');

    const grid          = document.getElementById('dashboard-' + uid + '-grid');
    const list          = document.getElementById('dashboard-' + uid + '-list');
    const modal         = document.getElementById('projectModal-' + uid);
    const modalTitle    = document.getElementById('projectModalLabel-' + uid);
    const modalBody     = document.getElementById('projectModalBody-' + uid);
    const searchInput   = container.querySelector('.conn-filter__search') as HTMLInputElement | null;
    const filterItems   = container.querySelectorAll<HTMLElement>('.dashboard__filter-item');
    const filterLabel   = container.querySelector('.dashboard__filter-label');
    const categoryItems = container.querySelectorAll<HTMLElement>('.conn-filter__category-item');
    const categoryLabel = container.querySelector('.conn-filter__category-label');
    const viewBtns      = container.querySelectorAll<HTMLElement>('.dashboard__view-btn');
    const statCards     = container.querySelectorAll<HTMLElement>('.dashboard__stat[data-status]');
    const resetBtn      = container.querySelector('.conn-filter__reset');
    const tagBadges     = container.querySelectorAll<HTMLElement>('.conn-filter__tag-badge');
    const emptyEl       = grid ? grid.querySelector('.dashboard__empty') : null;

    let currentFilter   = 'all';
    let currentCategory = 'all';
    let currentSearch   = '';
    let selectedTags    = new Set<string>();

    const setCategoryLabel = (item: HTMLElement): void => {
        if (!categoryLabel) return;
        const label = categoryLabel as HTMLElement;
        label.textContent = item.dataset.category === 'all'
            ? (label.dataset.default || item.textContent.trim())
            : item.textContent.trim();
    };

    const setStatusLabel = (item: HTMLElement): void => {
        if (!filterLabel) return;
        const label = filterLabel as HTMLElement;
        label.textContent = item.dataset.status === 'all'
            ? (label.dataset.default || item.textContent.trim())
            : item.textContent.trim();
    };

    // ─── Reset button visibility ────────────────────────────────────────────
    const syncResetBtn = (): void => {
        if (!resetBtn) return;
        const dirty = currentFilter !== 'all' || currentCategory !== 'all' || currentSearch !== '' || selectedTags.size > 0;
        (resetBtn as HTMLElement).hidden = !dirty;
    };

    // ─── Central filter setter ──────────────────────────────────────────────
    const setFilter = (status: string): void => {
        currentFilter = status;

        // Sync dropdown items + label
        filterItems.forEach(i => i.classList.remove('active'));
        const activeItem = [...filterItems].find(i => i.dataset.status === status);
        if (activeItem) {
            activeItem.classList.add('active');
            setStatusLabel(activeItem);
        }

        // Sync stat cards
        statCards.forEach(c => c.classList.remove('is-active'));
        const activeCard = [...statCards].find(c => c.dataset.status === status);
        if (activeCard) activeCard.classList.add('is-active');

        syncResetBtn();
        applyFilters();
    };

    // ─── Reset all filters ──────────────────────────────────────────────────
    const resetAll = (): void => {
        currentFilter   = 'all';
        currentCategory = 'all';
        currentSearch   = '';

        // Sync status UI
        filterItems.forEach(i => i.classList.remove('active'));
        const allStatusItem = [...filterItems].find(i => i.dataset.status === 'all');
        if (allStatusItem) {
            allStatusItem.classList.add('active');
            setStatusLabel(allStatusItem);
        }
        statCards.forEach(c => c.classList.remove('is-active'));
        const totalCard = [...statCards].find(c => c.dataset.status === 'all');
        if (totalCard) totalCard.classList.add('is-active');

        // Sync category UI
        categoryItems.forEach(i => i.classList.remove('active'));
        const allCatItem = [...categoryItems].find(i => i.dataset.category === 'all');
        if (allCatItem) {
            allCatItem.classList.add('active');
            setCategoryLabel(allCatItem);
        }

        // Sync search
        if (searchInput) searchInput.value = '';

        // Sync tags
        selectedTags.clear();
        tagBadges.forEach(b => b.classList.remove('is-active'));

        syncResetBtn();
        applyFilters();
    };

    // ─── Stats counter ──────────────────────────────────────────────────────
    const updateStats = (): void => {
        if (!grid) return;
        let total = 0, active = 0, inProgress = 0, comingSoon = 0, inactive = 0, deprecated = 0, archived = 0;
        grid.querySelectorAll<HTMLElement>('.dashboard__project-item').forEach(item => {
            if (item.classList.contains('dashboard__empty')) return;
            total++;
            const s = item.dataset.status;
            if (s === 'active')      active++;
            if (s === 'in_progress') inProgress++;
            if (s === 'coming_soon') comingSoon++;
            if (s === 'inactive')    inactive++;
            if (s === 'deprecated')  deprecated++;
            if (s === 'archived')    archived++;
        });
        const statEls = {
            total:       container.querySelector('[data-stat="total"]'),
            active:      container.querySelector('[data-stat="active"]'),
            in_progress: container.querySelector('[data-stat="in_progress"]'),
            coming_soon: container.querySelector('[data-stat="coming_soon"]'),
            inactive:    container.querySelector('[data-stat="inactive"]'),
            deprecated:  container.querySelector('[data-stat="deprecated"]'),
            archived:    container.querySelector('[data-stat="archived"]'),
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
            const card = container.querySelector(`.dashboard__stat[data-status="${status}"]`);
            if (card) card.classList.toggle('d-none', count === 0);
            const filterItem = container.querySelector(`.dashboard__filter-item[data-status="${status}"]`);
            if (filterItem) filterItem.classList.toggle('d-none', count === 0);
        };
        toggle('active',      active);
        toggle('in_progress', inProgress);
        toggle('coming_soon', comingSoon);
        toggle('inactive',    inactive);
        toggle('deprecated',  deprecated);
        toggle('archived',    archived);
    };

    // ─── Filter / search application ────────────────────────────────────────
    const applyFilters = (): void => {
        const q = currentSearch.toLowerCase().trim();
        let visibleGrid = 0;

        const matchesTags = (item: HTMLElement): boolean => {
            if (selectedTags.size === 0) return true;
            const itemTags = (item.dataset.tags || '').split(',').filter(Boolean);
            return [...selectedTags].every(t => itemTags.includes(t));
        };

        if (grid) {
            grid.querySelectorAll<HTMLElement>('.dashboard__project-item').forEach(item => {
                if (item.classList.contains('dashboard__empty')) return;
                const statusOk   = currentFilter === 'all' || item.dataset.status === currentFilter;
                const categoryOk = currentCategory === 'all' || item.dataset.category === currentCategory;
                const searchOk   = !q || (item.dataset.search || '').toLowerCase().includes(q);
                const tagsOk     = matchesTags(item);
                const show = statusOk && categoryOk && searchOk && tagsOk;
                item.classList.toggle('d-none', !show);
                if (show) visibleGrid++;
            });
        }

        if (list) {
            list.querySelectorAll<HTMLElement>('.dashboard__project-item').forEach(item => {
                const statusOk   = currentFilter === 'all' || item.dataset.status === currentFilter;
                const categoryOk = currentCategory === 'all' || item.dataset.category === currentCategory;
                const searchOk   = !q || (item.dataset.search || '').toLowerCase().includes(q);
                const tagsOk     = matchesTags(item);
                item.classList.toggle('d-none', !(statusOk && categoryOk && searchOk && tagsOk));
            });
        }

        if (emptyEl) emptyEl.classList.toggle('d-none', visibleGrid > 0);
    };

    // ─── Event listeners ─────────────────────────────────────────────────────
    filterItems.forEach(item => {
        item.addEventListener('click', (e: MouseEvent) => {
            e.preventDefault();
            setFilter(item.dataset.status ?? 'all');
        });
    });

    statCards.forEach(card => {
        const activate = (): void => {
            const status = card.dataset.status ?? 'all';
            // Toggle: clicking the active card resets to all
            setFilter(card.classList.contains('is-active') ? 'all' : status);
        };
        card.addEventListener('click', activate);
        card.addEventListener('keydown', (e: KeyboardEvent) => {
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); activate(); }
        });
    });

    if (resetBtn) {
        resetBtn.addEventListener('click', resetAll);
    }

    categoryItems.forEach(item => {
        item.addEventListener('click', (e: MouseEvent) => {
            e.preventDefault();
            categoryItems.forEach(i => i.classList.remove('active'));
            item.classList.add('active');
            currentCategory = item.dataset.category ?? 'all';
            setCategoryLabel(item);
            syncResetBtn();
            applyFilters();
        });
    });

    if (searchInput) {
        searchInput.addEventListener('input', () => {
            currentSearch = searchInput.value;
            syncResetBtn();
            applyFilters();
        });
    }

    tagBadges.forEach(badge => {
        badge.addEventListener('click', () => {
            const tag = (badge.dataset.tag ?? '').toLowerCase();
            if (selectedTags.has(tag)) {
                selectedTags.delete(tag);
                badge.classList.remove('is-active');
            } else {
                selectedTags.add(tag);
                badge.classList.add('is-active');
            }
            syncResetBtn();
            applyFilters();
        });
    });

    viewBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            viewBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            const view = btn.dataset.view;
            if (grid) grid.classList.toggle('d-none', view !== 'grid');
            if (list) list.classList.toggle('d-none', view !== 'list');
        });
    });

    container.querySelectorAll('.dashboard__visit-link').forEach(link => {
        link.addEventListener('click', (e: Event) => e.stopPropagation());
    });

    if (modal && modalBody) {
        modal.addEventListener('show.bs.modal', (e: Event) => {
            const trigger = (e as Event & { relatedTarget?: HTMLElement }).relatedTarget ?? null;
            if (!trigger) return;
            const detailId = trigger.getAttribute('data-project-detail');
            const title    = trigger.getAttribute('data-project-title') || '';
            if (modalTitle) modalTitle.textContent = title;
            if (detailId) {
                const detailEl = document.getElementById(detailId);
                if (detailEl) modalBody.innerHTML = detailEl.innerHTML;
            }
        });
    }

    // ─── Init ────────────────────────────────────────────────────────────────
    updateStats();
    setFilter('all'); // marks total card active, sets dropdown, applies filters

    const defaultView = container.dataset.defaultView || 'grid';
    viewBtns.forEach(btn => btn.classList.toggle('active', btn.dataset.view === defaultView));
    if (grid) grid.classList.toggle('d-none', defaultView !== 'grid');
    if (list) list.classList.toggle('d-none', defaultView !== 'list');
};

// ─── Self-init ───────────────────────────────────────────────────────────────
function onReady(fn: () => void): void {
    if (document.readyState !== 'loading') fn();
    else document.addEventListener('DOMContentLoaded', fn);
}

onReady(() => {
    document.querySelectorAll('.dashboard').forEach(el => initDashboard(el as HTMLElement));
});

export { initDashboard };
