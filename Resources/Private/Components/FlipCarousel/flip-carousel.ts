/**
 * Connetation Elements – GSAP Flip Carousel (Caterpillar)
 * Flex row of items; next/prev shifts the row with scale-in/out at the ends.
 */

import { gsap } from 'gsap';
import { Flip } from 'gsap/Flip';
import { attachSwipeHandler } from '../Shared/swipe';

gsap.registerPlugin(Flip);

class FlipCarousel {
    private static readonly CONTAINER_SEL        = '.conn-flip-carousel';
    private static readonly TRACK_SEL            = '.conn-flip-carousel__track';
    private static readonly BTN_NEXT_SEL         = '.conn-flip-carousel__btn--next';
    private static readonly BTN_PREV_SEL         = '.conn-flip-carousel__btn--prev';
    private static readonly ITEM_SEL             = '.conn-flip-carousel__item';
    private static readonly CLASS_ITEM           = 'conn-flip-carousel__item';
    private static readonly CLASS_HIDE           = 'hide';
    private static readonly DATASET_AUTOPLAY     = 'autoplay';
    private static readonly DATASET_DELAY        = 'autoplayDelay';
    private static readonly ENABLED_VALUE        = '1';

    private readonly track: HTMLElement;
    private readonly btnNext: Element | null;
    private readonly btnPrev: Element | null;
    private readonly hasAutoplay: boolean;
    private readonly autoplayDelay: number;
    private allItems: string[] = [];
    private total        = 0;
    private visibleCount = 0;
    private startIndex   = 0;
    private isAnimating  = false;
    private autoplayTimer: ReturnType<typeof setInterval> | null = null;

    constructor(private readonly el: HTMLElement) {
        this.track        = el.querySelector(FlipCarousel.TRACK_SEL) as HTMLElement;
        this.btnNext      = el.querySelector(FlipCarousel.BTN_NEXT_SEL);
        this.btnPrev      = el.querySelector(FlipCarousel.BTN_PREV_SEL);
        this.hasAutoplay  = el.dataset[FlipCarousel.DATASET_AUTOPLAY] === FlipCarousel.ENABLED_VALUE;
        this.autoplayDelay = parseInt(el.dataset[FlipCarousel.DATASET_DELAY] || '3000', 10);

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

    // ─── Autoplay ─────────────────────────────────────────────────────
    private startAutoplay(): void {
        this.stopAutoplay();
        this.autoplayTimer = setInterval(() => this.advance(true), this.autoplayDelay);
    }

    private stopAutoplay(): void {
        if (this.autoplayTimer !== null) {
            clearInterval(this.autoplayTimer);
            this.autoplayTimer = null;
        }
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
            if (this.hasAutoplay) this.startAutoplay(); // reset timer on manual advance
            this.advance(true);
        });
        if (this.btnPrev) this.btnPrev.addEventListener('click', () => {
            if (this.hasAutoplay) this.startAutoplay();
            this.advance(false);
        });

        // ─── Swipe / drag ─────────────────────────────────────────────────
        attachSwipeHandler(this.track, (forward) => {
            if (this.hasAutoplay) this.startAutoplay();
            this.advance(forward);
        }, { isLocked: () => this.isAnimating });

        // ─── Autoplay ─────────────────────────────────────────────────────
        if (this.hasAutoplay) {
            this.startAutoplay();
            this.track.addEventListener('mouseenter', () => this.stopAutoplay());
            this.track.addEventListener('mouseleave', () => this.startAutoplay());
        }
    }

    static initAll(): void {
        document.querySelectorAll<HTMLElement>(FlipCarousel.CONTAINER_SEL)
            .forEach(el => new FlipCarousel(el));
    }
}

document.addEventListener('DOMContentLoaded', () => FlipCarousel.initAll());
