/**
 * Connetation Elements – Wiki content element
 */

const initWiki = (container) => {
    const uid            = container.id.replace('wiki-', '').replace('-container', '');
    const navItems       = container.querySelectorAll('.wiki__nav-item');
    const panels         = container.querySelectorAll('.wiki__page-panel');
    const searchInput    = container.querySelector('.conn-filter__search');
    const categoryItems  = container.querySelectorAll('.conn-filter__category-item');
    const categoryLabel  = container.querySelector('.conn-filter__category-label');
    const tagBadges      = container.querySelectorAll('.conn-filter__tag-badge');
    const resetBtn       = container.querySelector('.conn-filter__reset');
    const navEmpty       = container.querySelector('.wiki__nav-empty');

    let currentCategory = 'all';
    let currentSearch   = '';
    let selectedTags    = new Set();

    const setCategoryLabel = (item) => {
        if (!categoryLabel) return;
        categoryLabel.textContent = item.dataset.category === 'all'
            ? (categoryLabel.dataset.default || item.textContent.trim())
            : item.textContent.trim();
    };

    // ─── Precompute full text of each page panel for search ─────────────────
    const panelText = new Map();
    panels.forEach(panel => {
        panelText.set(panel.dataset.pageUid, panel.textContent.toLowerCase());
    });

    // ─── Show a specific page panel ─────────────────────────────────────────
    const showPage = (pageUid) => {
        panels.forEach(p => {
            const active = p.dataset.pageUid === pageUid;
            p.classList.toggle('is-active', active);
            p.hidden = !active;
            p.setAttribute('aria-hidden', String(!active));
        });
        navItems.forEach(i => i.classList.toggle('is-active', i.dataset.pageUid === pageUid));
    };

    // ─── Show/hide reset button ──────────────────────────────────────────────
    const updateResetBtn = () => {
        if (!resetBtn) return;
        const dirty = currentCategory !== 'all' || selectedTags.size > 0 || currentSearch.trim() !== '';
        resetBtn.hidden = !dirty;
    };

    // ─── Apply all filters ───────────────────────────────────────────────────
    const applyFilter = () => {
        const q = currentSearch.toLowerCase().trim();
        let firstVisible = null;

        navItems.forEach(item => {
            const catOk    = currentCategory === 'all' || item.dataset.category === currentCategory;
            const searchOk = !q || (panelText.get(item.dataset.pageUid) || '').includes(q);

            let tagOk = true;
            if (selectedTags.size > 0) {
                const pageTags = item.dataset.tags ? item.dataset.tags.split(',') : [];
                tagOk = [...selectedTags].every(tag => pageTags.includes(tag));
            }

            const visible = catOk && searchOk && tagOk;
            item.classList.toggle('d-none', !visible);
            if (visible && !firstVisible) firstVisible = item.dataset.pageUid;
        });

        if (navEmpty) navEmpty.classList.toggle('d-none', firstVisible !== null);
        if (firstVisible) showPage(firstVisible);
        updateResetBtn();
    };

    // ─── Reset all filters ───────────────────────────────────────────────────
    const resetFilters = () => {
        currentCategory = 'all';
        currentSearch   = '';
        selectedTags.clear();

        if (searchInput) searchInput.value = '';

        categoryItems.forEach(i => i.classList.remove('active'));
        const allItem = [...categoryItems].find(i => i.dataset.category === 'all');
        if (allItem) {
            allItem.classList.add('active');
            setCategoryLabel(allItem);
        }

        tagBadges.forEach(b => b.classList.remove('is-active'));

        applyFilter();
    };

    // ─── Nav item click ──────────────────────────────────────────────────────
    navItems.forEach(item => {
        item.querySelector('.wiki__nav-btn')?.addEventListener('click', () => {
            showPage(item.dataset.pageUid);
        });
    });

    // ─── Category dropdown click ─────────────────────────────────────────────
    categoryItems.forEach(item => {
        item.addEventListener('click', e => {
            e.preventDefault();
            categoryItems.forEach(i => i.classList.remove('active'));
            item.classList.add('active');
            currentCategory = item.dataset.category;
            setCategoryLabel(item);
            updateResetBtn();
            applyFilter();
        });
    });

    // ─── Tag badge click (toggle, multi-select) ──────────────────────────────
    tagBadges.forEach(badge => {
        badge.addEventListener('click', () => {
            const tagName = badge.dataset.tag.toLowerCase();
            if (selectedTags.has(tagName)) {
                selectedTags.delete(tagName);
                badge.classList.remove('is-active');
            } else {
                selectedTags.add(tagName);
                badge.classList.add('is-active');
            }
            applyFilter();
        });
    });

    // ─── Search input ────────────────────────────────────────────────────────
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            currentSearch = searchInput.value;
            applyFilter();
        });
    }

    // ─── Reset button ────────────────────────────────────────────────────────
    if (resetBtn) {
        resetBtn.addEventListener('click', resetFilters);
    }

    // ─── Inject copy buttons into CKEditor code blocks ──────────────────────
    const ICON_COPY  = `<svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true"><path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/><path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h3zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z"/></svg>`;
    const ICON_CHECK = `<svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true"><path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/></svg>`;

    container.querySelectorAll('.wiki__block--code pre').forEach(pre => {
        const wrap = pre.closest('.wiki__code-wrap');
        if (!wrap || wrap.querySelector('.wiki__copy-btn')) return;

        const lang = pre.querySelector('code')?.className.replace('language-', '') || 'code';

        const header = document.createElement('div');
        header.className = 'wiki__code-header';
        header.innerHTML = `<span class="wiki__code-lang">${lang}</span>
            <button type="button" class="wiki__copy-btn" aria-label="Copy code">${ICON_COPY} Copy</button>`;
        wrap.insertBefore(header, pre);

        header.querySelector('.wiki__copy-btn').addEventListener('click', () => {
            const text = pre.querySelector('code')?.textContent ?? '';
            navigator.clipboard?.writeText(text).then(() => {
                const btn = header.querySelector('.wiki__copy-btn');
                btn.innerHTML = `${ICON_CHECK} Copied!`;
                btn.classList.add('is-copied');
                setTimeout(() => {
                    btn.innerHTML = `${ICON_COPY} Copy`;
                    btn.classList.remove('is-copied');
                }, 1500);
            });
        });
    });

    // ─── Init: show first page ───────────────────────────────────────────────
    if (navItems.length) showPage(navItems[0].dataset.pageUid);
};

// ─── Self-init ───────────────────────────────────────────────────────────────
function onReady(fn) {
    if (document.readyState !== 'loading') fn();
    else document.addEventListener('DOMContentLoaded', fn);
}

onReady(() => {
    document.querySelectorAll('.wiki').forEach(initWiki);
});
