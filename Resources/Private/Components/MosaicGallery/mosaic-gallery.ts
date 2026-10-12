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

/**
 * Connetation Elements – Mosaic Gallery
 *
 * Layout types handled by JS: masonry, justified.
 * Layout types handled by pure CSS: grid, metro, fisheye.
 * Metro closes an incomplete last group via CSS spans — no cloned items.
 *
 * Lightbox ("Open in lightbox" setting) is real GLightbox — it self-binds
 * to the `.glightbox` anchors rendered in Partials/MosaicGallery/Item.html
 * (see conn_t3wtk_base's main.js), no per-component wiring needed here.
 */

const GAP_MAP: Record<string, number> = { none: 0, small: 8, medium: 16, large: 24 };

class MosaicGallery {

    private readonly grid: HTMLElement;

    private resizeTimer: ReturnType<typeof setTimeout> | null = null;
    private layoutItems:  HTMLElement[] = [];

    private readonly layoutType: string;

    constructor(private readonly el: HTMLElement) {
        this.grid       = el.querySelector('.conn-mosaic-gallery__grid') as HTMLElement;
        this.layoutType = el.dataset.layout ?? 'masonry';

        // Apply aspect-ratio CSS variable for grid, masonry, and old instances without data-layout
        const aspect = el.dataset.aspect;
        if (aspect && aspect !== 'none') el.style.setProperty('--conn-mosaic-gallery-aspect', aspect);

        this.initLayout();
    }

    // ─── Layout init (masonry + justified require JS recalculation) ─
    private initLayout(): void {
        if (this.layoutType !== 'masonry' && this.layoutType !== 'justified') return;

        this.layoutItems = Array.from(
            this.grid.querySelectorAll<HTMLElement>('.conn-mosaic-gallery__item')
        );
        const imgs = Array.from(this.grid.querySelectorAll<HTMLImageElement>('img'));

        requestAnimationFrame(() => this.recalc());

        imgs.forEach(img => {
            if (!img.complete || img.naturalHeight === 0) {
                img.addEventListener('load',  () => this.scheduleRecalc(), { once: true });
                img.addEventListener('error', () => this.scheduleRecalc(), { once: true });
            }
        });

        window.addEventListener('resize', () => this.scheduleRecalc(), { passive: true });
    }

    private scheduleRecalc(): void {
        if (this.resizeTimer) clearTimeout(this.resizeTimer);
        this.resizeTimer = setTimeout(() => this.recalc(), 80);
    }

    private recalc(): void {
        if (this.layoutType === 'masonry')   this.calcMasonry();
        if (this.layoutType === 'justified') this.calcJustified();
    }

    // ─── Masonry: shortest-column-first absolute positioning ──────
    private calcMasonry(): void {
        const items   = this.layoutItems;
        const gapPx   = GAP_MAP[this.el.dataset.gap ?? 'medium'] ?? 16;
        const numCols = this.getColumnCount();
        const gridW   = this.grid.getBoundingClientRect().width;

        if (gridW === 0) return;

        const colW = (gridW - gapPx * (numCols - 1)) / numCols;
        const colH = new Array<number>(numCols).fill(0);

        items.forEach(item => { item.style.width = `${colW}px`; });
        void this.grid.getBoundingClientRect(); // force layout

        items.forEach(item => {
            const inner = item.querySelector<HTMLElement>('.conn-mosaic-gallery__item-inner');
            const h = inner ? inner.getBoundingClientRect().height : 0;
            if (h === 0) return;

            let col = 0;
            for (let i = 1; i < numCols; i++) {
                if (colH[i] < colH[col]) col = i;
            }

            item.style.left = `${col * (colW + gapPx)}px`;
            item.style.top  = `${colH[col]}px`;
            colH[col] += h + gapPx;
        });

        const maxH = Math.max(0, ...colH);
        this.grid.style.height = `${Math.max(0, maxH - gapPx)}px`;
    }

    // ─── Justified: equal-height rows that fill container width ───
    private calcJustified(): void {
        const items    = this.layoutItems;
        const gapPx    = GAP_MAP[this.el.dataset.gap ?? 'medium'] ?? 16;
        const gridW    = this.grid.getBoundingClientRect().width;
        const targetH  = parseInt(this.el.dataset.galleryHeight ?? '280', 10);

        if (gridW === 0) return;

        // Aspect ratio: prefer original image dimensions (data attrs from TYPO3 FAL),
        // fall back to naturalWidth/Height after load, then default 4:3.
        const aspects = items.map(item => {
            const dw = parseFloat(item.dataset.imgWidth  ?? '0');
            const dh = parseFloat(item.dataset.imgHeight ?? '0');
            if (dw && dh) return dw / dh;
            const img = item.querySelector<HTMLImageElement>('img');
            if (img?.naturalWidth && img.naturalHeight) return img.naturalWidth / img.naturalHeight;
            return 4 / 3;
        });

        // Row-fill: accumulate images until row width exceeds container
        const rows: number[][] = [];
        let i = 0;
        while (i < items.length) {
            let rowW = 0;
            let j    = i;
            while (j < items.length) {
                const gap = j > i ? gapPx : 0;
                const w   = targetH * aspects[j];
                if (rowW + gap + w > gridW && j > i) break;
                rowW += gap + w;
                j++;
            }
            rows.push(Array.from({ length: j - i }, (_, k) => i + k));
            i = j;
        }

        // Absolute-position every item at pixel-exact coordinates
        let y = 0;
        rows.forEach((row, ri) => {
            const totalGaps = (row.length - 1) * gapPx;
            const sumAR     = row.reduce((s, idx) => s + aspects[idx], 0);
            const rowH      = (gridW - totalGaps) / sumAR;
            let   x         = 0;

            row.forEach((idx, ci) => {
                const item = items[idx];
                const w    = rowH * aspects[idx];
                item.style.width  = `${w}px`;
                item.style.height = `${rowH}px`;
                item.style.left   = `${x}px`;
                item.style.top    = `${y}px`;
                x += w + (ci < row.length - 1 ? gapPx : 0);
            });

            y += rowH + (ri < rows.length - 1 ? gapPx : 0);
        });

        this.grid.style.height = `${y}px`;
    }

    private getColumnCount(): number {
        const w      = window.innerWidth;
        const target = parseInt(this.el.dataset.columns ?? '3', 10);
        if (w <= 575)  return 1;
        if (w <= 767)  return Math.min(2, target);
        if (w <= 991)  return Math.min(3, target);
        if (w <= 1199) return Math.min(4, target);
        return target;
    }

}

import { registry } from '../Shared/registry';
registry.register('.conn-mosaic-gallery', el => new MosaicGallery(el));
