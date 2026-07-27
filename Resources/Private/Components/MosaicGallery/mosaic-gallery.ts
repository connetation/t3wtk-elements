/**
 * Connetation Elements – Mosaic Gallery
 *
 * Layout types handled by JS: masonry, justified.
 * Layout types handled by pure CSS: grid, metro, fisheye.
 */

interface GalleryItem {
    src:     string;
    thumb:   string;
    title:   string;
    caption: string;
}

const GAP_MAP: Record<string, number> = { none: 0, small: 8, medium: 16, large: 24 };

class MosaicGallery {

    private readonly grid:      HTMLElement;
    private readonly lightbox:  HTMLElement | null;
    private readonly lbImg:     HTMLImageElement | null;
    private readonly lbCaption: HTMLElement | null;
    private readonly lbCounter: HTMLElement | null;
    private readonly btnPrev:   HTMLElement | null;
    private readonly btnNext:   HTMLElement | null;
    private readonly overlay:   HTMLElement | null;

    private items:        GalleryItem[] = [];
    private current      = -1;
    private isNavigating = false;
    private swipeStartX  = 0;
    private isSwiping    = false;
    private resizeTimer: ReturnType<typeof setTimeout> | null = null;
    private layoutItems:  HTMLElement[] = [];

    private readonly lightboxEnabled: boolean;
    private readonly layoutType:      string;

    constructor(private readonly el: HTMLElement) {
        this.grid            = el.querySelector('.conn-mosaic-gallery__grid') as HTMLElement;
        this.lightbox        = el.querySelector('.conn-mosaic-gallery__lightbox');
        this.lbImg           = el.querySelector<HTMLImageElement>('.conn-mosaic-gallery__lb-img');
        this.lbCaption       = el.querySelector('.conn-mosaic-gallery__lb-caption');
        this.lbCounter       = el.querySelector('.conn-mosaic-gallery__lb-counter');
        this.btnPrev         = el.querySelector('.conn-mosaic-gallery__lb-prev');
        this.btnNext         = el.querySelector('.conn-mosaic-gallery__lb-next');
        this.overlay         = el.querySelector('.conn-mosaic-gallery__lb-overlay');
        this.lightboxEnabled = el.dataset.lightbox === '1';
        this.layoutType      = el.dataset.layout ?? 'masonry';

        // Apply aspect-ratio CSS variable for grid, masonry, and old instances without data-layout
        const aspect = el.dataset.aspect;
        if (aspect && aspect !== 'none') el.style.setProperty('--conn-mosaic-gallery-aspect', aspect);

        this.collectItems();
        this.initLayout();
        if (this.lightboxEnabled && this.lightbox) this.initLightbox();
    }

    // ─── Metro: pad to next multiple of 5 by cycling from start ──
    private initMetro(): void {
        const items  = Array.from(this.grid.querySelectorAll<HTMLElement>('.conn-mosaic-gallery__item'));
        if (items.length === 0) return;

        const needed = (5 - (items.length % 5)) % 5;
        for (let i = 0; i < needed; i++) {
            const srcIndex = i % items.length;
            const clone    = items[srcIndex].cloneNode(true) as HTMLElement;
            clone.setAttribute('aria-hidden', 'true');
            if (this.lightboxEnabled) {
                clone.style.cursor = 'zoom-in';
                clone.addEventListener('click', () => this.open(srcIndex));
            }
            this.grid.appendChild(clone);
        }
    }

    // ─── Layout init (masonry + justified require JS recalculation) ─
    private initLayout(): void {
        if (this.layoutType === 'metro') { this.initMetro(); return; }
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

    // ─── Collect lightbox item data from DOM ──────────────────────
    private collectItems(): void {
        this.grid.querySelectorAll<HTMLElement>('.conn-mosaic-gallery__item').forEach((node, i) => {
            this.items.push({
                src:     node.dataset.src     ?? '',
                thumb:   node.dataset.thumb   ?? '',
                title:   node.dataset.title   ?? '',
                caption: node.dataset.caption ?? '',
            });
            if (this.lightboxEnabled) {
                node.addEventListener('click', () => this.open(i));
            }
        });
    }

    // ─── Lightbox: open ───────────────────────────────────────────
    private open(index: number): void {
        if (!this.lightbox) return;
        this.current = index;
        this.lightbox.classList.add('is-visible');
        document.body.style.overflow = 'hidden';
        this.loadImage(index);
    }

    // ─── Lightbox: close ──────────────────────────────────────────
    private close(): void {
        if (!this.lightbox) return;
        this.lightbox.classList.remove('is-visible');
        document.body.style.overflow = '';
        this.current = -1;
        if (this.lbImg) {
            this.lbImg.classList.remove('is-ready');
        }
    }

    // ─── Lightbox: navigate ───────────────────────────────────────
    private navigate(direction: number): void {
        if (this.current === -1 || this.isNavigating || !this.items.length) return;
        this.isNavigating = true;

        const next = ((this.current + direction) + this.items.length) % this.items.length;

        if (!this.lbImg) { this.isNavigating = false; return; }

        const slideOut = direction > 0 ? 'translateX(-36px)' : 'translateX(36px)';
        this.lbImg.style.transition = 'opacity 0.15s ease, transform 0.15s ease';
        this.lbImg.style.opacity    = '0';
        this.lbImg.style.transform  = slideOut;

        setTimeout(() => {
            this.current = next;
            this.loadImage(next, () => {
                if (!this.lbImg) return;
                const slideIn = direction > 0 ? 'translateX(36px)' : 'translateX(-36px)';
                this.lbImg.style.transition = 'none';
                this.lbImg.style.transform  = slideIn;
                this.lbImg.style.opacity    = '0';
                this.lbImg.classList.remove('is-ready');

                requestAnimationFrame(() => {
                    if (!this.lbImg) return;
                    this.lbImg.style.transition = 'opacity 0.2s ease, transform 0.2s ease';
                    this.lbImg.style.transform  = 'translateX(0)';
                    this.lbImg.style.opacity    = '1';
                    this.lbImg.classList.add('is-ready');
                    this.isNavigating = false;
                });
            });
        }, 170);
    }

    // ─── Lightbox: load full image ────────────────────────────────
    private loadImage(index: number, onLoad?: () => void): void {
        const item = this.items[index];
        if (!item || !this.lbImg) return;

        this.lbImg.classList.remove('is-ready');
        this.lbImg.style.transition = 'none';
        this.lbImg.style.opacity    = '0';
        this.lbImg.style.transform  = 'translateX(0)';

        const tmp    = new Image();
        tmp.onload = () => {
            if (!this.lbImg) return;
            this.lbImg.src = item.src;
            this.lbImg.alt = item.title;

            if (onLoad) {
                onLoad();
            } else {
                requestAnimationFrame(() => {
                    if (!this.lbImg) return;
                    this.lbImg.style.transition = 'opacity 0.25s ease';
                    this.lbImg.style.opacity    = '1';
                    this.lbImg.classList.add('is-ready');
                });
            }

            if (this.lbCaption) {
                this.lbCaption.textContent = item.caption || item.title || '';
            }
            if (this.lbCounter) {
                this.lbCounter.textContent = `${index + 1} / ${this.items.length}`;
            }
        };
        tmp.onerror = () => {
            // Fallback: show thumbnail
            if (!this.lbImg) return;
            this.lbImg.src = item.thumb;
            this.lbImg.classList.add('is-ready');
        };
        tmp.src = item.src;
    }

    // ─── Lightbox: event listeners ────────────────────────────────
    private initLightbox(): void {
        this.overlay?.addEventListener('click', () => this.close());
        this.btnPrev?.addEventListener('click',  () => this.navigate(-1));
        this.btnNext?.addEventListener('click',  () => this.navigate(1));

        document.addEventListener('keydown', (e: KeyboardEvent) => {
            if (this.current === -1) return;
            if (e.key === 'Escape')      this.close();
            if (e.key === 'ArrowRight')  this.navigate(1);
            if (e.key === 'ArrowLeft')   this.navigate(-1);
        });

        // Swipe / pointer drag
        this.lightbox!.addEventListener('pointerdown', (e: PointerEvent) => {
            this.swipeStartX = e.clientX;
            this.isSwiping   = true;
        }, { passive: true });

        window.addEventListener('pointerup', (e: PointerEvent) => {
            if (!this.isSwiping || this.current === -1) { this.isSwiping = false; return; }
            this.isSwiping = false;
            const dx = e.clientX - this.swipeStartX;
            if (Math.abs(dx) < 50) return;
            dx < 0 ? this.navigate(1) : this.navigate(-1);
        }, { passive: true });

        window.addEventListener('pointercancel', () => { this.isSwiping = false; }, { passive: true });
    }
}

import { registry } from '../Shared/registry';
registry.register('.conn-mosaic-gallery', el => new MosaicGallery(el));
