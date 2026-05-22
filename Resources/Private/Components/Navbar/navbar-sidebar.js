import { gsap } from 'gsap';

(function init() {
    const nav = document.getElementById('mainNav');
    if (!nav || !nav.classList.contains('conn-navbar-sidebar')) return;

    const direction = nav.dataset.sidebarDirection || 'left';
    const sidebar   = document.getElementById('mainNavSidebar');
    const overlay   = document.getElementById('mainNavOverlay');
    const toggler   = nav.querySelector('.conn-sidebar-toggler');

    if (!sidebar || !overlay || !toggler) return;

    // Read settings from data attributes on the sidebar element
    const allowScroll   = sidebar.dataset.scroll   === 'true';
    const backdropMode  = sidebar.dataset.backdrop; // 'true' | 'false' | 'static'
    const showBackdrop  = backdropMode !== 'false';
    const staticBackdrop = backdropMode === 'static';

    if (!showBackdrop) overlay.classList.add('conn-sidebar-overlay--hidden');

    const closeBtns = document.querySelectorAll('.conn-sidebar-close');
    const navItems  = sidebar.querySelectorAll('.conn-sidebar__item');
    const xOff      = direction === 'left' ? '-100%' : '100%';
    let isOpen = false;

    const open = () => {
        if (isOpen) return;
        isOpen = true;
        if (!allowScroll) document.body.style.overflow = 'hidden';
        toggler.setAttribute('aria-expanded', 'true');
        sidebar.setAttribute('aria-hidden', 'false');
        sidebar.style.visibility = 'visible';

        const tl = gsap.timeline();
        if (showBackdrop) {
            tl.to(overlay, { opacity: 1, visibility: 'visible', duration: 0.2, ease: 'none' });
        }
        tl.fromTo(sidebar, { x: xOff }, { x: '0%', duration: 0.35, ease: 'power3.out' }, showBackdrop ? '<0.05' : '0')
          .fromTo(navItems,
            { x: direction === 'left' ? -16 : 16, opacity: 0 },
            { x: 0, opacity: 1, duration: 0.25, stagger: 0.05, ease: 'power2.out' },
            '<0.2'
          );
    };

    const close = () => {
        if (!isOpen) return;
        isOpen = false;
        document.body.style.overflow = '';
        toggler.setAttribute('aria-expanded', 'false');
        sidebar.setAttribute('aria-hidden', 'true');

        const tl = gsap.timeline({
            onComplete: () => {
                if (showBackdrop) overlay.style.visibility = 'hidden';
                sidebar.style.visibility = 'hidden';
            }
        });
        tl.to(navItems, { opacity: 0, x: direction === 'left' ? -8 : 8, duration: 0.12, stagger: 0.025 })
          .to(sidebar,  { x: xOff, duration: 0.3, ease: 'power3.in' }, '<0.05');
        if (showBackdrop) {
            tl.to(overlay, { opacity: 0, duration: 0.2 }, '<0.1');
        }
    };

    toggler.addEventListener('click', () => isOpen ? close() : open());
    closeBtns.forEach(btn => btn.addEventListener('click', close));

    // Backdrop click only closes when not static
    if (showBackdrop && !staticBackdrop) {
        overlay.addEventListener('click', close);
    }

    document.addEventListener('keydown', e => { if (e.key === 'Escape' && isOpen) close(); });
})();
