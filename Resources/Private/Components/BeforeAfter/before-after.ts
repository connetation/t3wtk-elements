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
 * Conn Before/After Image Slider
 * Drag (mouse/touch via pointer events) or arrow keys move the reveal line
 * between the "after" image (full, underneath) and the "before" image
 * (clipped on top, clip-path shape driven by --conn-ba-position — see
 * _before-after.scss for the per-direction clip-path/divider formulas).
 */

type Direction = 'left-right' | 'top-bottom' | 'diagonal-tlbr' | 'diagonal-bltr';

class BeforeAfter {

    private static readonly STEP = 2;

    private readonly direction: Direction;
    private dragging = false;
    private resizeObserver: ResizeObserver | null = null;

    constructor(private readonly el: HTMLElement) {
        this.direction = (this.el.dataset.direction as Direction) || 'left-right';
        this.init();
    }

    private init(): void {
        const start = parseFloat(this.el.dataset.start ?? '50');
        this.setPosition(Number.isFinite(start) ? start : 50);

        this.el.addEventListener('pointerdown', (event) => this.startDrag(event));
        this.el.addEventListener('pointermove', (event) => this.onDrag(event));
        this.el.addEventListener('pointerup', () => this.endDrag());
        this.el.addEventListener('pointerleave', () => this.endDrag());
        this.el.addEventListener('keydown', (event) => this.onKeydown(event));

        if (this.direction === 'diagonal-tlbr' || this.direction === 'diagonal-bltr') {
            this.updateDiagonalAngle();
            this.resizeObserver = new ResizeObserver(() => this.updateDiagonalAngle());
            this.resizeObserver.observe(this.el);
        }
    }

    private startDrag(event: PointerEvent): void {
        this.dragging = true;
        this.el.setPointerCapture(event.pointerId);
        this.setPositionFromPointer(event.clientX, event.clientY);
    }

    private onDrag(event: PointerEvent): void {
        if (!this.dragging) return;
        this.setPositionFromPointer(event.clientX, event.clientY);
    }

    private endDrag(): void {
        this.dragging = false;
    }

    private onKeydown(event: KeyboardEvent): void {
        const current = this.getPosition();
        if (event.key === 'ArrowLeft' || event.key === 'ArrowUp') {
            this.setPosition(current - BeforeAfter.STEP);
            event.preventDefault();
        } else if (event.key === 'ArrowRight' || event.key === 'ArrowDown') {
            this.setPosition(current + BeforeAfter.STEP);
            event.preventDefault();
        }
    }

    private setPositionFromPointer(clientX: number, clientY: number): void {
        const rect = this.el.getBoundingClientRect();
        let pct: number;

        if (this.direction === 'top-bottom') {
            pct = ((clientY - rect.top) / rect.height) * 100;
        } else if (this.direction === 'diagonal-tlbr') {
            const dx = (clientX - rect.left) / rect.width;
            const dy = (clientY - rect.top) / rect.height;
            pct = ((dx + dy) / 2) * 100;
        } else if (this.direction === 'diagonal-bltr') {
            const dx = (clientX - rect.left) / rect.width;
            const dy = (clientY - rect.top) / rect.height;
            pct = ((dx + (1 - dy)) / 2) * 100;
        } else {
            pct = ((clientX - rect.left) / rect.width) * 100;
        }

        this.setPosition(pct);
    }

    private updateDiagonalAngle(): void {
        const rect = this.el.getBoundingClientRect();
        if (!rect.width || !rect.height) return;

        const angleDeg = Math.atan(rect.height / rect.width) * (180 / Math.PI);
        const sign = this.direction === 'diagonal-tlbr' ? -1 : 1;
        this.el.style.setProperty('--conn-ba-angle', `${sign * angleDeg}deg`);
    }

    private getPosition(): number {
        const value = this.el.style.getPropertyValue('--conn-ba-position') || '50%';
        return parseFloat(value);
    }

    private setPosition(pct: number): void {
        const clamped = Math.min(100, Math.max(0, pct));
        this.el.style.setProperty('--conn-ba-position', `${clamped}%`);
        this.el.setAttribute('aria-valuenow', String(Math.round(clamped)));
    }

}

import { registry } from '../Shared/registry';
registry.register('.conn-before-after__wrap', el => new BeforeAfter(el));
