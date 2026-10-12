/*
 * This file is part of the Connetation TYPO3 Web Toolkit - T3WTK.
 *
 * Copyright (c) Connetation Web Engineering GmbH
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
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

import AjaxRequest from '@typo3/core/ajax/ajax-request.js';

const MAX_TILES = 4000;
const IONICON_STYLE_SUFFIXES = { filled: '', outline: '-outline', sharp: '-sharp' };
const listCache = new Map();
const pickerState = new WeakMap();

// "library"/"style"/"name" aren't guaranteed to share a DOM ancestor — the
// "Icon" field can be rendered standalone (full width) in a different
// fieldset/palette than "library"/"style" — and some tables using this
// picker (e.g. a plain icon-only field with no library/style switcher) don't
// have all three at all. Every relevant element is looked up by its own
// TYPO3 field name, read from the picker's own data-*-field attributes
// (emitted by IconPreviewSelectElement from that field's real TCA column),
// instead of a hardcoded suffix or DOM proximity — this is what makes the
// same picker reusable for fields other than TextIcon's own three.
function fieldsFor(picker) {
    const prefix = picker.dataset.recordPrefix || '';
    const libraryField = picker.dataset.libraryField;
    const styleField = picker.dataset.styleField;
    const nameField = picker.dataset.nameField;
    return {
        library: libraryField ? document.querySelector(`select[name="${prefix}[${libraryField}]"]`) : null,
        style: styleField ? document.querySelector(`select[name="${prefix}[${styleField}]"]`) : null,
        name: nameField ? document.querySelector(`select[name="${prefix}[${nameField}]"]`) : null,
    };
}

async function stateFor(picker) {
    const fields = fieldsFor(picker);
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

function updatePreview(picker) {
    const icon = picker.querySelector('.conn-icon-preview i');
    if (!icon) {
        return;
    }
    const fields = fieldsFor(picker);
    const markup = iconMarkup(picker, fields.library?.value, fields.style?.value, fields.name?.value || '');
    applyIconMarkup(icon, markup);
}

async function fetchIconList(library, style) {
    const cacheKey = `${library}::${style}`;
    if (listCache.has(cacheKey)) {
        return listCache.get(cacheKey);
    }
    const response = await new AjaxRequest(TYPO3.settings.ajaxUrls.conn_t3wtk_elements_icon_list)
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
    document.querySelectorAll('.conn-icon-picker').forEach((picker) => {
        const fields = fieldsFor(picker);
        if (target === fields.library || target === fields.style) {
            updatePreview(picker);
        }
    });
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
                console.error('conn_t3wtk_elements: could not load icon list', error);
                const detail = error?.response ? `${error.response.status} ${error.response.statusText}` : (error?.message || error);
                status.textContent = `Could not load icons: ${detail}`;
            }
        }
        return;
    }

    const tile = event.target.closest('.conn-icon-picker__tile');
    if (tile) {
        const picker = tile.closest('.conn-icon-picker');
        const fields = fieldsFor(picker);
        if (fields.name) {
            fields.name.value = tile.dataset.iconName;
            fields.name.dispatchEvent(new Event('change', { bubbles: true }));
        }
        updatePreview(picker);
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
        console.error('conn_t3wtk_elements: could not filter icon list', error);
        const detail = error?.response ? `${error.response.status} ${error.response.statusText}` : (error?.message || error);
        status.textContent = `Could not load icons: ${detail}`;
    }
});
