/**
 * Connetation Elements – GSAP Flip Gallery Modal
 * Grid of box-content divs; clicking moves the element into the modal via GSAP Flip.
 * Based on SimpleGridFlipModal CodePen.
 */

import { gsap } from 'gsap';
import { Flip } from 'gsap/Flip';

gsap.registerPlugin(Flip);

class FlipGallery {

    private static readonly MODAL_SEL           = '.conn-flip-gallery__modal';
    private static readonly CONTENT_SEL         = '.conn-flip-gallery__content';
    private static readonly OVERLAY_SEL         = '.conn-flip-gallery__overlay';
    private static readonly BTN_PREV_SEL        = '.conn-flip-gallery__modal-prev';
    private static readonly BTN_NEXT_SEL        = '.conn-flip-gallery__modal-next';
    private static readonly BOX_SEL             = '.conn-flip-gallery__box';
    private static readonly BOX_CONTENT_SEL     = '.conn-flip-gallery__box-content';
    private static readonly KEY_ESCAPE          = 'Escape';
    private static readonly KEY_ARROW_RIGHT     = 'ArrowRight';
    private static readonly KEY_ARROW_LEFT      = 'ArrowLeft';

    // How much of the viewport the lightbox is allowed to fill, and the
    // aspect ratio it falls back to for an item missing width/height
    // metadata (e.g. an SVG or a very old import) — matches this element's
    // previous fixed 4:5 modal shape.
    private static readonly VIEWPORT_FRACTION   = 0.92;
    private static readonly FALLBACK_RATIO      = 4 / 5;

    private readonly modal: HTMLElement;
    private readonly modalContent: HTMLElement;
    private readonly modalOverlay: HTMLElement | null;
    private readonly btnPrev: Element | null;
    private readonly btnNext: Element | null;
    private readonly boxes: HTMLElement[];
    private readonly boxesContent: HTMLElement[];
    private boxIndex: number | undefined = undefined;
    private isNavigating = false;

    // swipeConsumed must be declared before the click handlers that reference it
    private swipeConsumed = false;

    constructor(private readonly el: HTMLElement) {
        this.modal        = el.querySelector(FlipGallery.MODAL_SEL) as HTMLElement;
        this.modalContent = this.modal?.querySelector(FlipGallery.CONTENT_SEL) as HTMLElement;
        this.modalOverlay = this.modal?.querySelector(FlipGallery.OVERLAY_SEL) as HTMLElement | null;
        this.btnPrev      = this.modal?.querySelector(FlipGallery.BTN_PREV_SEL);
        this.btnNext      = this.modal?.querySelector(FlipGallery.BTN_NEXT_SEL);
        this.boxes        = Array.from(el.querySelectorAll<HTMLElement>(FlipGallery.BOX_SEL));
        this.boxesContent = Array.from(el.querySelectorAll<HTMLElement>(FlipGallery.BOX_CONTENT_SEL));

        if (!this.boxes.length || !this.modal || !this.modalContent) return;

        this.init();
    }

    // ─── Open / close ─────────────────────────────────────────────────
    // The modal has no fixed shape of its own (see &__content in the SCSS —
    // just a max-width/max-height cap): this sizes it to whatever box is
    // currently open, preserving that image's own aspect ratio instead of
    // stretching/letterboxing every image into one fixed frame.
    private sizeContentTo(box: HTMLElement): void {
        const natW = parseFloat(box.dataset.natW ?? '');
        const natH = parseFloat(box.dataset.natH ?? '');
        const ratio = natW > 0 && natH > 0 ? natW / natH : FlipGallery.FALLBACK_RATIO;

        const maxW = window.innerWidth * FlipGallery.VIEWPORT_FRACTION;
        const maxH = window.innerHeight * FlipGallery.VIEWPORT_FRACTION;

        let width = maxW;
        let height = width / ratio;
        if (height > maxH) {
            height = maxH;
            width = height * ratio;
        }

        this.modalContent.style.width = `${width}px`;
        this.modalContent.style.height = `${height}px`;
    }

    private handleBoxClick(box: HTMLElement, i: number): void {
        if (this.swipeConsumed) { this.swipeConsumed = false; return; }

        if (this.boxIndex !== undefined) {
            // Close
            const state = Flip.getState(box);
            this.boxes[this.boxIndex].appendChild(box);
            this.boxIndex = undefined;
            gsap.to([this.modal, this.modalOverlay], { autoAlpha: 0, ease: 'power1.inOut', duration: 0.35 });
            Flip.from(state, {
                duration: 0.7,
                ease: 'power1.inOut',
                absolute: true,
                onComplete: () => gsap.set(box, { zIndex: 'auto' }),
            });
            gsap.set(box, { zIndex: 1002 });
        } else {
            // Open
            // sizeContentTo reads data-nat-w/-h, which live on the outer
            // .conn-flip-gallery__box (this.boxes[i]) — box here is the inner
            // .conn-flip-gallery__box-content (this.boxesContent[i], the one
            // actually reparented into the modal below) and carries neither.
            this.sizeContentTo(this.boxes[i]);
            const state = Flip.getState(box);
            this.modalContent.appendChild(box);
            this.boxIndex = i;
            gsap.set(this.modal, { autoAlpha: 1 });
            Flip.from(state, { duration: 0.7, ease: 'power1.inOut' });
            gsap.to(this.modalOverlay, { autoAlpha: 0.65, duration: 0.35 });
        }
    }

    // ─── Navigate prev / next ─────────────────────────────────────────
    // Simple slide+fade — Flip is only used for open/close, not navigation.
    private navigateTo(direction: number): void {
        if (this.boxIndex === undefined || this.isNavigating) return;
        const current = this.modalContent.querySelector<HTMLElement>(FlipGallery.BOX_CONTENT_SEL);
        if (!current) return;

        const nextIndex   = ((this.boxIndex + direction) + this.boxes.length) % this.boxes.length;
        const nextContent = this.boxes[nextIndex].querySelector<HTMLElement>(FlipGallery.BOX_CONTENT_SEL);
        if (!nextContent) return;

        this.isNavigating = true;

        gsap.to(current, {
            x: direction > 0 ? -50 : 50,
            opacity: 0,
            duration: 0.2,
            ease: 'power2.in',
            onComplete: () => {
                gsap.set(current, { x: 0, opacity: 1 });
                this.boxes[this.boxIndex as number].appendChild(current);
                this.boxIndex = nextIndex;
                this.sizeContentTo(this.boxes[nextIndex]);
                this.modalContent.appendChild(nextContent);
                gsap.fromTo(nextContent,
                    { x: direction > 0 ? 50 : -50, opacity: 0 },
                    { x: 0, opacity: 1, duration: 0.25, ease: 'power2.out',
                      onComplete: () => { this.isNavigating = false; } }
                );
            },
        });
    }

    private init(): void {
        this.boxesContent.forEach((box, i) => {
            box.addEventListener('click', () => this.handleBoxClick(box, i));
        });

        if (this.btnPrev) this.btnPrev.addEventListener('click', () => this.navigateTo(-1));
        if (this.btnNext) this.btnNext.addEventListener('click', () => this.navigateTo(1));

        // ─── Overlay click closes ─────────────────────────────────────────
        if (this.modalOverlay) {
            this.modalOverlay.addEventListener('click', () => {
                if (this.boxIndex === undefined) return;
                const box = this.modalContent.querySelector<HTMLElement>(FlipGallery.BOX_CONTENT_SEL);
                if (box) box.click();
            });
        }

        // ─── Keyboard ────────────────────────────────────────────────────
        document.addEventListener('keydown', (e: KeyboardEvent) => {
            if (this.boxIndex === undefined) return;
            if (e.key === FlipGallery.KEY_ESCAPE) {
                const box = this.modalContent.querySelector<HTMLElement>(FlipGallery.BOX_CONTENT_SEL);
                if (box) box.click();
            }
            if (e.key === FlipGallery.KEY_ARROW_RIGHT) this.navigateTo(1);
            if (e.key === FlipGallery.KEY_ARROW_LEFT)  this.navigateTo(-1);
        });

        // ─── Resize: keep the open image's own size correct ────────────────
        window.addEventListener('resize', () => {
            if (this.boxIndex === undefined) return;
            this.sizeContentTo(this.boxes[this.boxIndex]);
        });

        // ─── Swipe / drag support ─────────────────────────────────────────
        // Use window for pointerup so fast swipes that end outside the modal
        // are still detected. isSwiping guards against unrelated pointer events.
        let swipeStartX = 0, isSwiping = false;

        this.modal.addEventListener('pointerdown', (e: PointerEvent) => {
            swipeStartX = e.clientX;
            isSwiping   = true;
        }, { passive: true });

        window.addEventListener('pointerup', (e: PointerEvent) => {
            if (!isSwiping) return;
            isSwiping = false;
            if (this.boxIndex === undefined) return;
            const dx = e.clientX - swipeStartX;
            if (Math.abs(dx) < 50) return;
            this.swipeConsumed = true;
            if (dx < 0) this.navigateTo(1);
            else        this.navigateTo(-1);
        }, { passive: true });

        window.addEventListener('pointercancel', () => { isSwiping = false; }, { passive: true });
    }

}

import { registry } from '../Shared/registry';
registry.register('.conn-flip-gallery', el => new FlipGallery(el));
