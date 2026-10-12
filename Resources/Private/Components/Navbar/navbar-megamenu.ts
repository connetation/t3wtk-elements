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

class NavbarMegamenu {
    private static readonly NAV_ID          = 'mainNav';
    private static readonly NAV_CLASS       = 'conn-navbar-megamenu';
    private static readonly TRIGGER_SEL     = '.conn-megamenu-trigger';
    private static readonly TOGGLE_SEL      = '.conn-megamenu-toggle';
    private static readonly CLASS_IS_OPEN   = 'is-open';
    private static readonly BREAKPOINT_DESKTOP = 992;

    private readonly triggers: NodeListOf<HTMLElement>;
    private activePanel: HTMLElement | null = null;
    private closeTimer: ReturnType<typeof setTimeout> | null = null;

    constructor(private readonly nav: HTMLElement) {
        this.triggers = nav.querySelectorAll<HTMLElement>(NavbarMegamenu.TRIGGER_SEL);
        if (!this.triggers.length) return; // no items with sub-menus
        this.init();
    }

    private getPanel(trigger: HTMLElement): HTMLElement | null {
        return this.nav.querySelector<HTMLElement>(`[data-megamenu-panel="${trigger.dataset.megamenu}"]`);
    }

    private openPanel(panel: HTMLElement, trigger: HTMLElement): void {
        if (this.activePanel === panel) return;
        if (this.activePanel) this.closePanel(this.activePanel, true);
        if (this.closeTimer) clearTimeout(this.closeTimer);
        this.activePanel = panel;
        trigger.classList.add(NavbarMegamenu.CLASS_IS_OPEN);
        gsap.killTweensOf(panel);
        gsap.fromTo(panel,
            { opacity: 0, y: -8 },
            { opacity: 1, y: 0, duration: 0.22, ease: 'power2.out',
              onStart: () => { panel.style.visibility = 'visible'; panel.style.pointerEvents = 'auto'; } }
        );
    }

    private closePanel(panel: HTMLElement, instant = false): void {
        const trigger = this.nav.querySelector<HTMLElement>(`[data-megamenu="${panel.dataset.megamenuPanel}"]`);
        if (trigger) trigger.classList.remove(NavbarMegamenu.CLASS_IS_OPEN);
        if (this.activePanel === panel) this.activePanel = null;
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
    }

    private init(): void {
        this.triggers.forEach(trigger => {
            const panel = this.getPanel(trigger);
            if (!panel) return;

            trigger.addEventListener('mouseenter', () => {
                if (this.closeTimer) clearTimeout(this.closeTimer);
                this.openPanel(panel, trigger);
            });
            trigger.addEventListener('mouseleave', () => {
                this.closeTimer = setTimeout(() => this.closePanel(panel), 180);
            });
            panel.addEventListener('mouseenter', () => { if (this.closeTimer) clearTimeout(this.closeTimer); });
            panel.addEventListener('mouseleave', () => {
                this.closeTimer = setTimeout(() => this.closePanel(panel), 180);
            });

            // Touch / keyboard: click trigger link toggles panel
            trigger.querySelector(NavbarMegamenu.TOGGLE_SEL)?.addEventListener('click', (e: Event) => {
                if (window.innerWidth >= NavbarMegamenu.BREAKPOINT_DESKTOP) {
                    e.preventDefault();
                    this.activePanel === panel ? this.closePanel(panel) : this.openPanel(panel, trigger);
                }
            });
        });

        // Close when clicking outside the nav
        document.addEventListener('click', (e: MouseEvent) => {
            if (this.activePanel && !this.nav.contains(e.target as Node)) this.closePanel(this.activePanel);
        });

        // Close on Escape
        document.addEventListener('keydown', (e: KeyboardEvent) => {
            if (e.key === 'Escape' && this.activePanel) this.closePanel(this.activePanel);
        });
    }

    static initAll(): void {
        const nav = document.getElementById(NavbarMegamenu.NAV_ID);
        if (!nav || !nav.classList.contains(NavbarMegamenu.NAV_CLASS)) return;
        new NavbarMegamenu(nav);
    }
}

import { registry } from '../Shared/registry';
registry.registerPage(() => NavbarMegamenu.initAll());
