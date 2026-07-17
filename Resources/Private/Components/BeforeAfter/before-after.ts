/**
 * Conn Before/After Image Slider
 * Drag (mouse/touch via pointer events) or arrow keys move the reveal line
 * between the "after" image (full, underneath) and the "before" image
 * (clipped on top, clip-path width driven by --conn-ba-position).
 */

class BeforeAfter {

    private static readonly STEP = 2;

    private dragging = false;

    constructor(private readonly el: HTMLElement) {
        this.init();
    }

    private init(): void {
        this.el.addEventListener('pointerdown', (event) => this.startDrag(event));
        this.el.addEventListener('pointermove', (event) => this.onDrag(event));
        this.el.addEventListener('pointerup', () => { this.dragging = false; });
        this.el.addEventListener('pointerleave', () => { this.dragging = false; });
        this.el.addEventListener('keydown', (event) => this.onKeydown(event));
    }

    private startDrag(event: PointerEvent): void {
        this.dragging = true;
        this.el.setPointerCapture(event.pointerId);
        this.setPositionFromClientX(event.clientX);
    }

    private onDrag(event: PointerEvent): void {
        if (!this.dragging) return;
        this.setPositionFromClientX(event.clientX);
    }

    private onKeydown(event: KeyboardEvent): void {
        const current = this.getPosition();
        if (event.key === 'ArrowLeft') {
            this.setPosition(current - BeforeAfter.STEP);
            event.preventDefault();
        } else if (event.key === 'ArrowRight') {
            this.setPosition(current + BeforeAfter.STEP);
            event.preventDefault();
        }
    }

    private setPositionFromClientX(clientX: number): void {
        const rect = this.el.getBoundingClientRect();
        const pct = ((clientX - rect.left) / rect.width) * 100;
        this.setPosition(pct);
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
