import AjaxRequest from '@typo3/core/ajax/ajax-request.js';

const FIELD_SUFFIX_PATTERN = /^(.*)\[tx_conn_elements_text_icon_(?:library|style|name)\]$/;
const MAX_TILES = 4000;
const IONICON_STYLE_SUFFIXES = { filled: '', outline: '-outline', sharp: '-sharp' };
const listCache = new Map();
const pickerState = new WeakMap();

// "library"/"style"/"name" aren't guaranteed to share a DOM ancestor — the
// "Icon" field can be rendered standalone (full width) in a different
// fieldset/palette than "library"/"style". Every relevant element is looked
// up by its exact TYPO3 field name instead of by DOM proximity.
function prefixFromFieldName(name) {
    const match = FIELD_SUFFIX_PATTERN.exec(name || '');
    return match ? match[1] : null;
}

function fieldsByPrefix(prefix) {
    return {
        library: document.querySelector(`select[name="${prefix}[tx_conn_elements_text_icon_library]"]`),
        style: document.querySelector(`select[name="${prefix}[tx_conn_elements_text_icon_style]"]`),
        name: document.querySelector(`select[name="${prefix}[tx_conn_elements_text_icon_name]"]`),
    };
}

function pickerForPrefix(prefix) {
    return [...document.querySelectorAll('.conn-icon-picker')].find((el) => el.dataset.recordPrefix === prefix) || null;
}

async function stateFor(picker) {
    const prefix = picker.dataset.recordPrefix || '';
    const fields = fieldsByPrefix(prefix);
    const library = fields.library?.value || 'bootstrap-icons';
    const style = fields.style?.value || 'solid';
    const cached = pickerState.get(picker);
    if (cached && cached.library === library && cached.style === style) {
        return cached;
    }
    const items = await fetchIconList(library, style);
    const state = { items, library, style };
    pickerState.set(picker, state);
    return state;
}

function iconMarkup(picker, library, style, name, url) {
    if (!name) {
        return { class: '', mask: '' };
    }
    if (library === 'font-awesome') {
        return { class: `fa-${style || 'solid'} fa-${name}`, mask: '' };
    }
    if (library === 'ionicons') {
        const suffix = IONICON_STYLE_SUFFIXES[style] ?? '';
        const base = url ? null : (picker?.dataset.ioniconsBase || '');
        const resolvedUrl = url || `${base}${name}${suffix}.svg`;
        return { class: 'conn-icon--ionicon', mask: `url(${resolvedUrl})` };
    }
    return { class: `bi bi-${name}`, mask: '' };
}

function applyIconMarkup(icon, markup) {
    icon.className = markup.class;
    icon.style.setProperty('--conn-icon-mask', markup.mask);
}

function updatePreview(prefix) {
    const picker = pickerForPrefix(prefix);
    const icon = picker?.querySelector('.conn-icon-preview i');
    if (!icon) {
        return;
    }
    const fields = fieldsByPrefix(prefix);
    const markup = iconMarkup(picker, fields.library?.value, fields.style?.value, fields.name?.value || '');
    applyIconMarkup(icon, markup);
}

async function fetchIconList(library, style) {
    const cacheKey = `${library}::${style}`;
    if (listCache.has(cacheKey)) {
        return listCache.get(cacheKey);
    }
    const response = await new AjaxRequest(TYPO3.settings.ajaxUrls.conn_elements_icon_list)
        .withQueryArguments({ library, style })
        .get();
    const items = await response.resolve();
    listCache.set(cacheKey, items);
    return items;
}

function renderGrid(picker, items, library, style) {
    const grid = picker.querySelector('.conn-icon-picker__grid');
    const status = picker.querySelector('.conn-icon-picker__status');
    grid.innerHTML = '';
    const fragment = document.createDocumentFragment();

    items.slice(0, MAX_TILES).forEach((item) => {
        const tile = document.createElement('button');
        tile.type = 'button';
        tile.className = 'conn-icon-picker__tile';
        tile.title = item.label;
        tile.dataset.iconName = item.name;
        const markup = iconMarkup(picker, library, style, item.name, item.url);
        const icon = document.createElement('i');
        applyIconMarkup(icon, markup);
        icon.setAttribute('aria-hidden', 'true');
        const label = document.createElement('span');
        label.textContent = item.label;
        tile.append(icon, label);
        fragment.append(tile);
    });
    grid.append(fragment);

    if (items.length === 0) {
        status.textContent = 'No icons match your search.';
    } else if (items.length > MAX_TILES) {
        status.textContent = `Showing ${MAX_TILES} of ${items.length} icons — refine your search to see more.`;
    } else {
        status.textContent = `${items.length} icon${items.length === 1 ? '' : 's'}`;
    }
}

document.addEventListener('change', (event) => {
    const target = event.target;
    if (!(target instanceof HTMLSelectElement)) {
        return;
    }
    const prefix = prefixFromFieldName(target.name);
    if (prefix === null) {
        return;
    }
    updatePreview(prefix);
});

document.addEventListener('click', async (event) => {
    const toggle = event.target.closest('.conn-icon-picker__toggle');
    if (toggle) {
        const picker = toggle.closest('.conn-icon-picker');
        const panel = picker.querySelector('.conn-icon-picker__panel');
        const opening = panel.hidden;
        panel.hidden = !opening;
        if (opening) {
            const search = picker.querySelector('.conn-icon-picker__search');
            const status = picker.querySelector('.conn-icon-picker__status');
            search.value = '';
            status.textContent = 'Loading icons…';
            try {
                const state = await stateFor(picker);
                renderGrid(picker, state.items, state.library, state.style);
                search.focus();
            } catch (error) {
                console.error('conn_elements: could not load icon list', error);
                const detail = error?.response ? `${error.response.status} ${error.response.statusText}` : (error?.message || error);
                status.textContent = `Could not load icons: ${detail}`;
            }
        }
        return;
    }

    const tile = event.target.closest('.conn-icon-picker__tile');
    if (tile) {
        const picker = tile.closest('.conn-icon-picker');
        const fields = fieldsByPrefix(picker.dataset.recordPrefix || '');
        if (fields.name) {
            fields.name.value = tile.dataset.iconName;
            fields.name.dispatchEvent(new Event('change', { bubbles: true }));
        }
        picker.querySelector('.conn-icon-picker__panel').hidden = true;
    }
});

document.addEventListener('input', async (event) => {
    const search = event.target.closest('.conn-icon-picker__search');
    if (!search) {
        return;
    }
    const picker = search.closest('.conn-icon-picker');
    const status = picker.querySelector('.conn-icon-picker__status');
    const term = search.value.trim().toLowerCase();
    try {
        const state = await stateFor(picker);
        const filtered = term === ''
            ? state.items
            : state.items.filter((item) => item.name.toLowerCase().includes(term) || item.label.toLowerCase().includes(term));
        renderGrid(picker, filtered, state.library, state.style);
    } catch (error) {
        console.error('conn_elements: could not filter icon list', error);
        const detail = error?.response ? `${error.response.status} ${error.response.statusText}` : (error?.message || error);
        status.textContent = `Could not load icons: ${detail}`;
    }
});
