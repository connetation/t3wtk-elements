/**
 * Conn Alert/Notice Banner — floating positions
 * Static (inline) alerts need no JS at all — Bootstrap's own alert.js
 * (already loaded globally) handles the dismiss button.
 *
 * Floating + compact ("icon only") alerts additionally need: toggling the
 * popover panel open/closed, closing on outside click / Escape, and — since
 * dismissing leaves an empty floating button otherwise — removing the whole
 * floating widget once its inner alert is dismissed.
 */

class AlertFloat {

    private readonly toggle: HTMLElement | null;

    constructor(private readonly el: HTMLElement) {
        this.toggle = el.querySelector('.conn-alert-float__toggle');
        this.init();
    }

    private init(): void {
        if (this.toggle) {
            this.toggle.addEventListener('click', (event) => {
                event.stopPropagation();
                this.isOpen() ? this.close() : this.open();
            });

            document.addEventListener('click', (event) => {
                if (this.isOpen() && !this.el.contains(event.target as Node)) {
                    this.close();
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && this.isOpen()) {
                    this.close();
                }
            });
        }

        // Dismissing the inner Bootstrap alert (btn-close) would otherwise
        // leave a floating widget with nothing left inside it.
        this.el.querySelector('.alert')?.addEventListener('closed.bs.alert', () => {
            this.el.remove();
        });
    }

    private isOpen(): boolean {
        return this.el.classList.contains('is-open');
    }

    private open(): void {
        this.el.classList.add('is-open');
        this.toggle?.setAttribute('aria-expanded', 'true');
    }

    private close(): void {
        this.el.classList.remove('is-open');
        this.toggle?.setAttribute('aria-expanded', 'false');
    }

}

import { registry } from '../Shared/registry';
registry.register('.conn-alert-float', el => new AlertFloat(el));
