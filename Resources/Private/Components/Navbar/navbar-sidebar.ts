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

import { gsap } from 'gsap';

class NavbarSidebar {
    private static readonly NAV_ID                   = 'mainNav';
    private static readonly NAV_CLASS                = 'conn-navbar-sidebar';
    private static readonly SIDEBAR_ID               = 'mainNavSidebar';
    private static readonly OVERLAY_ID               = 'mainNavOverlay';
    private static readonly TOGGLER_SEL              = '.conn-sidebar-toggler';
    private static readonly CLOSE_BTN_SEL            = '.conn-sidebar-close';
    private static readonly NAV_ITEMS_SEL            = '.conn-sidebar__item';
    private static readonly CLASS_OVERLAY_HIDDEN     = 'conn-sidebar-overlay--hidden';
    private static readonly DATASET_SIDEBAR_DIR      = 'sidebarDirection';
    private static readonly DATASET_SCROLL           = 'scroll';
    private static readonly DATASET_BACKDROP         = 'backdrop';
    private static readonly BACKDROP_FALSE           = 'false';
    private static readonly BACKDROP_STATIC          = 'static';
    private static readonly BACKDROP_TRUE_VALUE      = 'true';

    private readonly direction: string;
    private readonly xOff: string;
    private readonly allowScroll: boolean;
    private readonly showBackdrop: boolean;
    private readonly staticBackdrop: boolean;
    private readonly closeBtns: NodeListOf<Element>;
    private readonly navItems: NodeListOf<Element>;
    private isOpen = false;

    // Guards are validated in initAll() before construction, so all args are guaranteed non-null.
    private constructor(
        private readonly nav: HTMLElement,
        private readonly sidebar: HTMLElement,
        private readonly overlay: HTMLElement,
        private readonly toggler: HTMLElement,
    ) {
        this.direction = nav.dataset[NavbarSidebar.DATASET_SIDEBAR_DIR] || 'left';

        const backdropMode   = sidebar.dataset[NavbarSidebar.DATASET_BACKDROP];
        this.allowScroll     = sidebar.dataset[NavbarSidebar.DATASET_SCROLL] === NavbarSidebar.BACKDROP_TRUE_VALUE;
        this.showBackdrop    = backdropMode !== NavbarSidebar.BACKDROP_FALSE;
        this.staticBackdrop  = backdropMode === NavbarSidebar.BACKDROP_STATIC;
        this.closeBtns       = document.querySelectorAll(NavbarSidebar.CLOSE_BTN_SEL);
        this.navItems        = sidebar.querySelectorAll(NavbarSidebar.NAV_ITEMS_SEL);
        this.xOff            = this.direction === 'left' ? '-100%' : '100%';

        if (!this.showBackdrop) overlay.classList.add(NavbarSidebar.CLASS_OVERLAY_HIDDEN);

        this.init();
    }

    private open(): void {
        if (this.isOpen) return;
        this.isOpen = true;
        if (!this.allowScroll) document.body.style.overflow = 'hidden';
        this.toggler.setAttribute('aria-expanded', 'true');
        this.sidebar.setAttribute('aria-hidden', 'false');
        this.sidebar.style.visibility = 'visible';

        const tl = gsap.timeline();
        if (this.showBackdrop) {
            tl.to(this.overlay, { opacity: 1, visibility: 'visible', duration: 0.2, ease: 'none' });
        }
        tl.fromTo(this.sidebar, { x: this.xOff }, { x: '0%', duration: 0.35, ease: 'power3.out' }, this.showBackdrop ? '<0.05' : '0')
          .fromTo(this.navItems,
            { x: this.direction === 'left' ? -16 : 16, opacity: 0 },
            { x: 0, opacity: 1, duration: 0.25, stagger: 0.05, ease: 'power2.out' },
            '<0.2'
          );
    }

    private close(): void {
        if (!this.isOpen) return;
        this.isOpen = false;
        document.body.style.overflow = '';
        this.toggler.setAttribute('aria-expanded', 'false');
        this.sidebar.setAttribute('aria-hidden', 'true');

        const tl = gsap.timeline({
            onComplete: () => {
                if (this.showBackdrop) this.overlay.style.visibility = 'hidden';
                this.sidebar.style.visibility = 'hidden';
            }
        });
        tl.to(this.navItems, { opacity: 0, x: this.direction === 'left' ? -8 : 8, duration: 0.12, stagger: 0.025 })
          .to(this.sidebar,  { x: this.xOff, duration: 0.3, ease: 'power3.in' }, '<0.05');
        if (this.showBackdrop) {
            tl.to(this.overlay, { opacity: 0, duration: 0.2 }, '<0.1');
        }
    }

    private init(): void {
        this.toggler.addEventListener('click', () => this.isOpen ? this.close() : this.open());
        this.closeBtns.forEach(btn => btn.addEventListener('click', () => this.close()));

        if (this.showBackdrop && !this.staticBackdrop) {
            this.overlay.addEventListener('click', () => this.close());
        }

        document.addEventListener('keydown', (e: KeyboardEvent) => { if (e.key === 'Escape' && this.isOpen) this.close(); });
    }

    static initAll(): void {
        const nav = document.getElementById(NavbarSidebar.NAV_ID);
        if (!nav || !nav.classList.contains(NavbarSidebar.NAV_CLASS)) return;
        const sidebar = document.getElementById(NavbarSidebar.SIDEBAR_ID) as HTMLElement | null;
        const overlay = document.getElementById(NavbarSidebar.OVERLAY_ID) as HTMLElement | null;
        const toggler = nav.querySelector<HTMLElement>(NavbarSidebar.TOGGLER_SEL);
        if (!sidebar || !overlay || !toggler) return;
        new NavbarSidebar(nav, sidebar, overlay, toggler);
    }
}

import { registry } from '../Shared/registry';
registry.registerPage(() => NavbarSidebar.initAll());
