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
