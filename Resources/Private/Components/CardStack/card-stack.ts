/**
 * Connetation Elements – GSAP Card Stack
 * Stacked deck of cards; Flip plugin animates transitions.
 * Explicit JS positioning via positionCards() — not CSS nth-last-child.
 */

import { gsap } from 'gsap';
import { Flip } from 'gsap/Flip';
import { attachSwipeHandler } from '../Shared/swipe';
import { AutoplayController } from '../Shared/autoplay';
import { registry } from '../Shared/registry';

gsap.registerPlugin(Flip);

class CardStack {

    private static readonly DECK_SEL        = '.conn-card-stack__deck';
    private static readonly BTN_PREV_SEL    = '.conn-card-stack__btn--prev';
    private static readonly BTN_NEXT_SEL    = '.conn-card-stack__btn--next';
    private static readonly COUNTER_SEL     = '.conn-card-stack__counter';
    private static readonly CARD_SEL        = ':scope > .conn-card-stack__card';
    private static readonly KEY_ARROW_RIGHT = 'ArrowRight';
    private static readonly KEY_ARROW_LEFT  = 'ArrowLeft';
    private static readonly OFFSET          = 20;
    private static readonly MAX_STACK_DEPTH = 4;

    private readonly deck: HTMLElement;
    private readonly btnPrev: Element | null;
    private readonly btnNext: Element | null;
    private readonly counter: Element | null;
    private readonly autoplay: AutoplayController;
    private totalCards  = 0;
    private currentCard = 1;
    private isAnimating = false;

    constructor(private readonly el: HTMLElement) {
        this.deck    = el.querySelector(CardStack.DECK_SEL) as HTMLElement;
        this.btnPrev = el.querySelector(CardStack.BTN_PREV_SEL);
        this.btnNext = el.querySelector(CardStack.BTN_NEXT_SEL);
        this.counter = el.querySelector(CardStack.COUNTER_SEL);
        this.autoplay = new AutoplayController(el, () => this.advance(true));

        if (!this.deck) return;
        if (!this.getVisible().length) return;

        this.totalCards = this.getVisible().length;
        this.init();
    }

    private getVisible(): HTMLElement[] {
        return Array.from(this.deck.querySelectorAll<HTMLElement>(CardStack.CARD_SEL))
                    .filter(c => c.style.display !== 'none');
    }

    private updateCounter(): void {
        if (this.counter) this.counter.textContent = `${this.currentCard} / ${this.totalCards}`;
    }

    private positionCards(cards: HTMLElement[]): void {
        cards.forEach((card, i) => {
            const fromFront = cards.length - 1 - i;
            const depth     = Math.min(fromFront, CardStack.MAX_STACK_DEPTH);
            gsap.set(card, {
                left:   depth * CardStack.OFFSET,
                top:  -(depth * CardStack.OFFSET),
                zIndex: i + 1,
            });
        });
    }

    private setHeight(): void {
        const visible = this.getVisible();
        const front = visible[visible.length - 1];
        if (front) this.deck.style.height = front.offsetHeight + 'px';
    }

    private init(): void {
        this.positionCards(this.getVisible());
        this.setHeight();
        this.updateCounter();
        window.addEventListener('resize', () => { this.positionCards(this.getVisible()); this.setHeight(); });

        // ─── Buttons + keyboard ───────────────────────────────────────────
        if (this.btnNext) this.btnNext.addEventListener('click', () => {
            this.autoplay.start(); // reset countdown on manual advance
            this.advance(true);
        });
        if (this.btnPrev) this.btnPrev.addEventListener('click', () => {
            this.autoplay.start();
            this.advance(false);
        });
        this.el.addEventListener('keydown', (e: KeyboardEvent) => {
            if (e.key === CardStack.KEY_ARROW_RIGHT) { this.autoplay.start(); this.advance(true); }
            if (e.key === CardStack.KEY_ARROW_LEFT)  { this.autoplay.start(); this.advance(false); }
        });

        // ─── Swipe / drag ─────────────────────────────────────────────────
        attachSwipeHandler(this.deck, (forward) => {
            this.autoplay.start();
            this.advance(forward);
        }, { isLocked: () => this.isAnimating });

        // ─── Autoplay ─────────────────────────────────────────────────────
        this.autoplay.start();
        if (this.autoplay.enabled) this.autoplay.attachHoverPause(this.deck);
    }

    // ─── Advance ──────────────────────────────────────────────────────
    private advance(forward: boolean): void {
        if (this.isAnimating) return;
        const cards = this.getVisible();
        if (cards.length <= 1) return;
        this.isAnimating = true;

        this.currentCard = forward
            ? (this.currentCard % this.totalCards) + 1
            : ((this.currentCard - 2 + this.totalCards) % this.totalCards) + 1;
        this.updateCounter();

        const state = Flip.getState(cards);
        let leavingCard: HTMLElement;

        if (forward) {
            leavingCard = cards[cards.length - 1];
            const clone = leavingCard.cloneNode(true);
            this.deck.insertBefore(clone, this.deck.firstChild);
            leavingCard.style.display = 'none';
        } else {
            leavingCard = cards[0];
            const clone = leavingCard.cloneNode(true);
            this.deck.appendChild(clone);
            leavingCard.style.display = 'none';
        }

        const newVisible = this.getVisible();
        this.positionCards(newVisible);

        Flip.from(state, {
            targets: [...newVisible, leavingCard],
            duration: 0.5,
            ease: 'sine.inOut',
            onEnter: (elements) => gsap.from(elements, {
                duration: 0.3,
                yPercent: forward ? 20 : -20,
                opacity: 0,
                ease: 'expo.out',
            }),
            onLeave: (elements) => gsap.to(elements, {
                duration: 0.3,
                yPercent: 5,
                xPercent: forward ? -5 : 5,
                transformOrigin: forward ? 'bottom left' : 'bottom right',
                opacity: 0,
                ease: 'expo.out',
                onComplete: () => {
                    if (elements[0]?.parentNode) elements[0].parentNode.removeChild(elements[0]);
                    this.setHeight();
                },
            }),
            onComplete: () => { this.isAnimating = false; },
        });
    }

}

registry.register('.conn-card-stack', el => new CardStack(el));
