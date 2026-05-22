import { gsap } from 'gsap';

const animateDashboard = (container: HTMLElement): void => {
    const stats = container.querySelectorAll('.dashboard__stat');
    const items = container.querySelectorAll('.dashboard__project-item:not(.dashboard__empty)');

    if (stats.length) {
        gsap.from(stats, {
            opacity: 0,
            y: 16,
            duration: 0.45,
            stagger: 0.08,
            ease: 'power2.out',
            clearProps: 'all',
        });
    }

    if (items.length) {
        gsap.from(items, {
            opacity: 0,
            y: 20,
            duration: 0.4,
            stagger: 0.05,
            ease: 'power2.out',
            delay: 0.3,
            clearProps: 'all',
        });
    }
};

// ─── Self-init ───────────────────────────────────────────────────────────────
function onReady(fn: () => void): void {
    if (document.readyState !== 'loading') fn();
    else document.addEventListener('DOMContentLoaded', fn);
}

onReady(() => {
    document.querySelectorAll('.dashboard').forEach(el => animateDashboard(el as HTMLElement));
});
