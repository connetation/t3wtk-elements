import { gsap } from 'gsap';

class DashboardAnimations {
    private static readonly CONTAINER_SEL    = '.dashboard';
    private static readonly STAT_SEL         = '.dashboard__stat';
    private static readonly PROJECT_ITEM_SEL = '.dashboard__project-item:not(.dashboard__empty)';

    static init(): void {
        document.querySelectorAll<HTMLElement>(DashboardAnimations.CONTAINER_SEL).forEach(container => {
            const stats = container.querySelectorAll(DashboardAnimations.STAT_SEL);
            const items = container.querySelectorAll(DashboardAnimations.PROJECT_ITEM_SEL);

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
        });
    }
}

document.addEventListener('DOMContentLoaded', () => DashboardAnimations.init());
