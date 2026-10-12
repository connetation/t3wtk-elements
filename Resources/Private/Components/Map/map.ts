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
 * Connetation Elements – Map & Location
 *
 * Builds the Google Maps embed client-side from a plain address string, so
 * the server never has to URL-encode anything and — more importantly — so
 * nothing is requested from Google until either the page loads with
 * "Click to load" off, or the visitor explicitly clicks "Load map".
 */

class ConnMap {

    constructor(private readonly el: HTMLElement) {
        this.init();
    }

    private init(): void {
        if (this.el.dataset.provider !== 'google') {
            return;
        }

        if (this.el.dataset.autoLoad === '1') {
            this.loadMap();
            return;
        }

        const loadButton = this.el.querySelector<HTMLButtonElement>('.conn-map__load-button');
        loadButton?.addEventListener('click', () => this.loadMap());
    }

    private loadMap(): void {
        const address = (this.el.dataset.address ?? '').trim();
        const zoom = this.el.dataset.zoom ?? '15';
        if (!address) {
            return;
        }

        const iframe = document.createElement('iframe');
        iframe.className = 'conn-map__iframe';
        iframe.loading = 'lazy';
        iframe.referrerPolicy = 'no-referrer-when-downgrade';
        iframe.src = `https://www.google.com/maps?q=${encodeURIComponent(address)}&z=${encodeURIComponent(zoom)}&output=embed`;

        const decorative = this.el.querySelector('.conn-map__decorative');
        decorative?.remove();
        this.el.appendChild(iframe);
    }

}

import { registry } from '../Shared/registry';
registry.register('.conn-map__canvas', (el) => new ConnMap(el));
