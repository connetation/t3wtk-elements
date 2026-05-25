import { gsap } from 'gsap';
import { registry } from '../Shared/registry';

class DashboardAnimations {
    private static readonly STAT_SEL         = '.dashboard__stat';
    private static readonly PROJECT_ITEM_SEL = '.dashboard__project-item:not(.dashboard__empty)';

    static initContainer(container: HTMLElement): void {
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
    }
}

registry.register('.dashboard', el => DashboardAnimations.initContainer(el));
