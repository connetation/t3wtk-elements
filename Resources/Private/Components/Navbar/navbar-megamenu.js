import { gsap } from 'gsap';

(function init() {
    const nav = document.getElementById('mainNav');
    if (!nav || !nav.classList.contains('conn-navbar-megamenu')) return;

    const triggers = nav.querySelectorAll('.conn-megamenu-trigger');
    if (!triggers.length) return; // no items with sub-menus

    let activePanel = null;
    let closeTimer  = null;

    const getPanel = (trigger) =>
        nav.querySelector(`[data-megamenu-panel="${trigger.dataset.megamenu}"]`);

    const openPanel = (panel, trigger) => {
        if (activePanel === panel) return;
        if (activePanel) closePanel(activePanel, true);
        clearTimeout(closeTimer);
        activePanel = panel;
        trigger.classList.add('is-open');
        gsap.killTweensOf(panel);
        gsap.fromTo(panel,
            { opacity: 0, y: -8 },
            { opacity: 1, y: 0, duration: 0.22, ease: 'power2.out',
              onStart: () => { panel.style.visibility = 'visible'; panel.style.pointerEvents = 'auto'; } }
        );
    };

    const closePanel = (panel, instant = false) => {
        const trigger = nav.querySelector(`[data-megamenu="${panel.dataset.megamenuPanel}"]`);
        if (trigger) trigger.classList.remove('is-open');
        if (activePanel === panel) activePanel = null;
        gsap.killTweensOf(panel);
        if (instant) {
            gsap.set(panel, { opacity: 0, y: -8 });
            panel.style.visibility = 'hidden';
            panel.style.pointerEvents = 'none';
        } else {
            gsap.to(panel, {
                opacity: 0, y: -4, duration: 0.18, ease: 'power2.in',
                onComplete: () => {
                    panel.style.visibility = 'hidden';
                    panel.style.pointerEvents = 'none';
                }
            });
        }
    };

    triggers.forEach(trigger => {
        const panel = getPanel(trigger);
        if (!panel) return;

        trigger.addEventListener('mouseenter', () => {
            clearTimeout(closeTimer);
            openPanel(panel, trigger);
        });
        trigger.addEventListener('mouseleave', () => {
            closeTimer = setTimeout(() => closePanel(panel), 180);
        });
        panel.addEventListener('mouseenter', () => clearTimeout(closeTimer));
        panel.addEventListener('mouseleave', () => {
            closeTimer = setTimeout(() => closePanel(panel), 180);
        });

        // Touch / keyboard: click trigger link toggles panel
        trigger.querySelector('.conn-megamenu-toggle')?.addEventListener('click', e => {
            if (window.innerWidth >= 992) {
                e.preventDefault();
                activePanel === panel ? closePanel(panel) : openPanel(panel, trigger);
            }
        });
    });

    // Close when clicking outside the nav
    document.addEventListener('click', e => {
        if (activePanel && !nav.contains(e.target)) closePanel(activePanel);
    });

    // Close on Escape
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && activePanel) closePanel(activePanel);
    });
})();
