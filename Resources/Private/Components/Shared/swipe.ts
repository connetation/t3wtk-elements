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

export interface SwipeOptions {
    threshold?: number;
    isLocked?: () => boolean;
}

/**
 * Attaches pointer-based swipe/drag detection to `target`.
 * setPointerCapture keeps events flowing even when the cursor leaves the element mid-drag.
 */
export function attachSwipeHandler(
    target: HTMLElement,
    onSwipe: (forward: boolean) => void,
    options: SwipeOptions = {}
): void {
    const { threshold = 50, isLocked = () => false } = options;
    let startX = 0, active = false;

    target.addEventListener('pointerdown', (e: PointerEvent) => {
        if (isLocked() || (e.pointerType === 'mouse' && e.button !== 0)) return;
        startX = e.clientX;
        active = true;
        target.setPointerCapture(e.pointerId);
        document.body.style.cursor = 'grabbing';
        e.preventDefault();
    }, { passive: false });

    target.addEventListener('pointerup', (e: PointerEvent) => {
        if (!active) return;
        active = false;
        document.body.style.cursor = '';
        const dx = e.clientX - startX;
        if (Math.abs(dx) >= threshold) onSwipe(dx < 0);
    });

    target.addEventListener('pointercancel', () => {
        active = false;
        document.body.style.cursor = '';
    });
}
