/**
 * Connetation Elements – GSAP Flip Carousel (Caterpillar)
 * Flex row of items; next/prev shifts the row with scale-in/out at the ends.
 */

import { gsap } from 'gsap';
import { Flip } from 'gsap/Flip';
import { attachSwipeHandler } from '../Shared/swipe';
import { AutoplayController } from '../Shared/autoplay';
import { registry } from '../Shared/registry';

gsap.registerPlugin(Flip);

class FlipCarousel {

    private static readonly TRACK_SEL     = '.conn-flip-carousel__track';
    private static readonly BTN_NEXT_SEL  = '.conn-flip-carousel__btn--next';
    private static readonly BTN_PREV_SEL  = '.conn-flip-carousel__btn--prev';
    private static readonly ITEM_SEL      = '.conn-flip-carousel__item';
    private static readonly CLASS_ITEM    = 'conn-flip-carousel__item';
    private static readonly CLASS_HIDE    = 'hide';

    private readonly track: HTMLElement;
    private readonly btnNext: Element | null;
    private readonly btnPrev: Element | null;
    private readonly autoplay: AutoplayController;
    private allItems: string[] = [];
    private total        = 0;
    private visibleCount = 0;
    private startIndex   = 0;
    private isAnimating  = false;

    constructor(private readonly el: HTMLElement) {
        this.track    = el.querySelector(FlipCarousel.TRACK_SEL) as HTMLElement;
        this.btnNext  = el.querySelector(FlipCarousel.BTN_NEXT_SEL);
        this.btnPrev  = el.querySelector(FlipCarousel.BTN_PREV_SEL);
        this.autoplay = new AutoplayController(el, () => this.advance(true));

        if (!this.track) return;

        const cols = parseInt(this.track.dataset.items ?? '4', 10) || 4;
        this.track.style.setProperty('--conn-items', String(cols));

        // Snapshot all item HTML; DOM is rebuilt from this for circular reuse
        this.allItems = Array.from(this.track.querySelectorAll(FlipCarousel.ITEM_SEL))
            .map(item => item.innerHTML);
        this.total        = this.allItems.length;
        this.visibleCount = this.total < 2 ? 0 : Math.min(cols, this.total);

        if (this.total < 2) return;

        // Rebuild track with initial visible window
        this.track.innerHTML = '';
        for (let i = 0; i < this.visibleCount; i++) {
            const item = document.createElement('div');
            item.className = FlipCarousel.CLASS_ITEM;
            item.innerHTML = this.allItems[i];
            this.track.appendChild(item);
        }

        this.init();
    }

    private makeItem(index: number): HTMLElement {
        const item = document.createElement('div');
        item.className = FlipCarousel.CLASS_ITEM;
        item.innerHTML = this.allItems[((index % this.total) + this.total) % this.total];
        return item;
    }

    // ─── Advance ──────────────────────────────────────────────────────
    private advance(forward: boolean): void {
        if (this.isAnimating) return;
        const items = Array.from(this.track.querySelectorAll(FlipCarousel.ITEM_SEL));
        this.isAnimating = true;

        const state = Flip.getState(items);

        let leaveEl: Element;
        if (forward) {
            this.track.append(this.makeItem(this.startIndex + this.visibleCount));
            leaveEl = items[0];
            leaveEl.classList.add(FlipCarousel.CLASS_HIDE);
            this.startIndex = (this.startIndex + 1) % this.total;
        } else {
            this.track.prepend(this.makeItem(this.startIndex - 1));
            leaveEl = items[items.length - 1];
            leaveEl.classList.add(FlipCarousel.CLASS_HIDE);
            this.startIndex = ((this.startIndex - 1) % this.total + this.total) % this.total;
        }

        Flip.from(state, {
            targets: this.track.querySelectorAll(FlipCarousel.ITEM_SEL),
            duration: 0.45,
            ease: 'power2.inOut',
            absoluteOnLeave: true,
            onComplete: () => {
                leaveEl.parentNode?.removeChild(leaveEl);
                this.isAnimating = false;
            },
            onEnter: (els) => gsap.from(els, {
                duration: 0.45,
                opacity: 0,
                scale: 0,
                transformOrigin: forward ? 'bottom right' : 'bottom left',
                ease: 'power3.out',
            }),
            onLeave: (els) => gsap.to(els, {
                duration: 0.35,
                opacity: 0,
                scale: 0,
                transformOrigin: forward ? 'bottom left' : 'bottom right',
                ease: 'power2.in',
            }),
        });
    }

    private init(): void {
        // ─── Buttons ──────────────────────────────────────────────────────
        if (this.btnNext) this.btnNext.addEventListener('click', () => {
            this.autoplay.start(); // reset countdown on manual advance
            this.advance(true);
        });
        if (this.btnPrev) this.btnPrev.addEventListener('click', () => {
            this.autoplay.start();
            this.advance(false);
        });

        // ─── Swipe / drag ─────────────────────────────────────────────────
        attachSwipeHandler(this.track, (forward) => {
            this.autoplay.start();
            this.advance(forward);
        }, { isLocked: () => this.isAnimating });

        // ─── Autoplay ─────────────────────────────────────────────────────
        this.autoplay.start();
        if (this.autoplay.enabled) this.autoplay.attachHoverPause(this.track);
    }

}

registry.register('.conn-flip-carousel', el => new FlipCarousel(el));
