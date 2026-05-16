/**
 * Connetation Elements – Dashboard content element
 */

const initDashboard = (container) => {
    const uid = container.id.replace('dashboard-', '').replace('-container', '');

    const grid          = document.getElementById('dashboard-' + uid + '-grid');
    const list          = document.getElementById('dashboard-' + uid + '-list');
    const modal         = document.getElementById('projectModal-' + uid);
    const modalTitle    = document.getElementById('projectModalLabel-' + uid);
    const modalBody     = document.getElementById('projectModalBody-' + uid);
    const searchInput   = container.querySelector('.conn-filter__search');
    const filterItems   = container.querySelectorAll('.dashboard__filter-item');
    const filterLabel   = container.querySelector('.dashboard__filter-label');
    const categoryItems = container.querySelectorAll('.conn-filter__category-item');
    const categoryLabel = container.querySelector('.conn-filter__category-label');
    const viewBtns      = container.querySelectorAll('.dashboard__view-btn');
    const statCards     = container.querySelectorAll('.dashboard__stat[data-status]');
    const resetBtn      = container.querySelector('.conn-filter__reset');
    const tagBadges     = container.querySelectorAll('.conn-filter__tag-badge');
    const emptyEl       = grid ? grid.querySelector('.dashboard__empty') : null;

    let currentFilter   = 'all';
    let currentCategory = 'all';
    let currentSearch   = '';
    let selectedTags    = new Set();

    const setCategoryLabel = (item) => {
        if (!categoryLabel) return;
        categoryLabel.textContent = item.dataset.category === 'all'
            ? (categoryLabel.dataset.default || item.textContent.trim())
            : item.textContent.trim();
    };

    const setStatusLabel = (item) => {
        if (!filterLabel) return;
        filterLabel.textContent = item.dataset.status === 'all'
            ? (filterLabel.dataset.default || item.textContent.trim())
            : item.textContent.trim();
    };

    // ─── Reset button visibility ────────────────────────────────────────────
    const syncResetBtn = () => {
        if (!resetBtn) return;
        const dirty = currentFilter !== 'all' || currentCategory !== 'all' || currentSearch !== '' || selectedTags.size > 0;
        resetBtn.hidden = !dirty;
    };

    // ─── Central filter setter ──────────────────────────────────────────────
    const setFilter = (status) => {
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
    const resetAll = () => {
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
    const updateStats = () => {
        if (!grid) return;
        let total = 0, active = 0, inProgress = 0, comingSoon = 0;
        grid.querySelectorAll('.dashboard__project-item').forEach(item => {
            if (item.classList.contains('dashboard__empty')) return;
            total++;
            const s = item.dataset.status;
            if (s === 'active')      active++;
            if (s === 'in_progress') inProgress++;
            if (s === 'coming_soon') comingSoon++;
        });
        const statEls = {
            total:       container.querySelector('[data-stat="total"]'),
            active:      container.querySelector('[data-stat="active"]'),
            in_progress: container.querySelector('[data-stat="in_progress"]'),
            coming_soon: container.querySelector('[data-stat="coming_soon"]'),
        };
        if (statEls.total)       statEls.total.textContent       = total;
        if (statEls.active)      statEls.active.textContent      = active;
        if (statEls.in_progress) statEls.in_progress.textContent = inProgress;
        if (statEls.coming_soon) statEls.coming_soon.textContent = comingSoon;
    };

    // ─── Filter / search application ────────────────────────────────────────
    const applyFilters = () => {
        const q = currentSearch.toLowerCase().trim();
        let visibleGrid = 0;

        const matchesTags = (item) => {
            if (selectedTags.size === 0) return true;
            const itemTags = (item.dataset.tags || '').split(',').filter(Boolean);
            return [...selectedTags].every(t => itemTags.includes(t));
        };

        if (grid) {
            grid.querySelectorAll('.dashboard__project-item').forEach(item => {
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
            list.querySelectorAll('.dashboard__project-item').forEach(item => {
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
        item.addEventListener('click', e => {
            e.preventDefault();
            setFilter(item.dataset.status);
        });
    });

    statCards.forEach(card => {
        const activate = () => {
            const status = card.dataset.status;
            // Toggle: clicking the active card resets to all
            setFilter(card.classList.contains('is-active') ? 'all' : status);
        };
        card.addEventListener('click', activate);
        card.addEventListener('keydown', e => {
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); activate(); }
        });
    });

    if (resetBtn) {
        resetBtn.addEventListener('click', resetAll);
    }

    categoryItems.forEach(item => {
        item.addEventListener('click', e => {
            e.preventDefault();
            categoryItems.forEach(i => i.classList.remove('active'));
            item.classList.add('active');
            currentCategory = item.dataset.category;
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
            const tag = badge.dataset.tag.toLowerCase();
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
        link.addEventListener('click', e => e.stopPropagation());
    });

    if (modal && modalBody) {
        modal.addEventListener('show.bs.modal', e => {
            const trigger = e.relatedTarget;
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
function onReady(fn) {
    if (document.readyState !== 'loading') fn();
    else document.addEventListener('DOMContentLoaded', fn);
}

onReady(() => {
    document.querySelectorAll('.dashboard.section').forEach(initDashboard);
});

export { initDashboard };
