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

import FormEngine from '@typo3/backend/form-engine.js';

// Backend for renderType "connResponsiveRange" (ResponsiveRangeElement.php):
// one slider, three device tabs. The hidden field holds "desktop,tablet,mobile";
// an empty tablet/mobile value inherits from the next larger device — or, with
// data-auto="1", is shown as "Auto" (the element lays it out automatically).
const DEVICES = ['desktop', 'tablet', 'mobile'];

class ResponsiveRange {
    constructor(id) {
        const root = document.getElementById(id);
        if (root && !root.dataset.initialized) {
            root.dataset.initialized = '1';
            this.init(root);
        }
    }

    init(root) {
        this.root = root;
        this.hidden = root.querySelector('.conn-responsive-range__value');
        this.slider = root.querySelector('.conn-responsive-range__slider');
        this.number = root.querySelector('.conn-responsive-range__number input');
        this.hint = root.querySelector('.conn-responsive-range__hint');
        this.device = root.querySelector('.conn-responsive-range__device');
        this.reset = root.querySelector('.conn-responsive-range__reset');
        this.tabs = [...root.querySelectorAll('.conn-responsive-range__tab')];
        this.unit = root.dataset.unit || '';
        this.fallback = root.dataset.default || this.slider.min;
        this.auto = root.dataset.auto === '1';
        this.active = 'desktop';

        const parts = String(this.hidden.value).split(',').map((part) => part.trim());
        this.values = {
            desktop: this.clamp(parts[0] || this.fallback),
            tablet: parts[1] ? this.clamp(parts[1]) : '',
            mobile: parts[2] ? this.clamp(parts[2]) : '',
        };

        this.tabs.forEach((tab) => tab.addEventListener('click', () => {
            this.active = tab.dataset.device;
            this.render();
        }));
        this.slider.addEventListener('input', () => this.set(this.slider.value));
        this.number.addEventListener('change', () => this.set(this.number.value));
        this.reset.addEventListener('click', () => this.set(''));
        this.render();
    }

    clamp(value) {
        const min = Number(this.slider.min);
        const max = Number(this.slider.max);
        const number = Number(value);
        if (Number.isNaN(number)) {
            return String(min);
        }
        return String(Math.min(max, Math.max(min, Math.round(number))));
    }

    effective(device) {
        const index = DEVICES.indexOf(device);
        for (let i = index; i >= 0; i--) {
            if (this.values[DEVICES[i]] !== '') {
                return this.values[DEVICES[i]];
            }
        }
        return this.fallback;
    }

    set(value) {
        if (value === '' || value === null) {
            // Desktop can't inherit — clearing it falls back to the default.
            this.values[this.active] = this.active === 'desktop' ? this.clamp(this.fallback) : '';
        } else {
            this.values[this.active] = this.clamp(value);
        }

        // "70,,45" — trailing empty (inherited) values are left out.
        const serialized = DEVICES.map((device) => this.values[device]);
        while (serialized.length > 1 && serialized[serialized.length - 1] === '') {
            serialized.pop();
        }
        const newValue = serialized.join(',');
        if (newValue !== this.hidden.value) {
            this.hidden.value = newValue;
            FormEngine.markFieldAsChanged(this.hidden);
        }
        this.render();
    }

    render() {
        this.tabs.forEach((tab) => {
            const device = tab.dataset.device;
            const isActive = device === this.active;
            tab.classList.toggle('active', isActive);
            tab.setAttribute('aria-selected', String(isActive));
            tab.classList.toggle('is-inherited', device !== 'desktop' && this.values[device] === '');
            const isAuto = this.auto && device !== 'desktop' && this.values[device] === '';
            tab.querySelector('.conn-responsive-range__badge').textContent = isAuto
                ? this.root.dataset.labelAuto
                : this.effective(device) + this.unit;
        });

        // e.g. "Tablet: 768–1199 px screen width" — ranges come from Devices.php
        this.device.textContent = this.root.dataset['range' + this.active.charAt(0).toUpperCase() + this.active.slice(1)] || '';

        const inherited = this.active !== 'desktop' && this.values[this.active] === '';
        const effective = this.effective(this.active);
        this.slider.value = effective;
        this.number.value = inherited ? '' : this.values[this.active];
        this.number.placeholder = effective;
        this.reset.hidden = this.active === 'desktop' || inherited;

        if (inherited && this.auto) {
            this.hint.textContent = this.root.dataset.labelInheritAuto;
        } else if (inherited) {
            this.hint.textContent = this.active === 'tablet'
                ? this.root.dataset.labelInheritDesktop
                : (this.values.tablet !== '' ? this.root.dataset.labelInheritTablet : this.root.dataset.labelInheritDesktop);
        } else {
            this.hint.textContent = '';
        }
    }
}

export default ResponsiveRange;
