/**
 * Connetation Elements – Testimonials
 * carousel: Swiper with navigation, pagination, autoplay.
 * grid: GSAP stagger entrance on scroll.
 */

import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay } from 'swiper/modules';
import { readAutoplay, buildSwiperAutoplay, ENABLED_VALUE } from '../Shared/autoplay';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { registry } from '../Shared/registry';
import { DESKTOP_MIN, TABLET_MIN } from '../Shared/devices';

gsap.registerPlugin(ScrollTrigger);

class Testimonials {

    private static readonly SWIPER_SEL     = '.conn-testimonials__swiper';
    private static readonly BTN_PREV_SEL   = '.conn-testimonials__btn-prev';
    private static readonly BTN_NEXT_SEL   = '.conn-testimonials__btn-next';
    private static readonly PAGINATION_SEL = '.conn-testimonials__pagination';
    private static readonly CARD_SEL       = '.conn-testimonials__card';
    private static readonly STARS_SEL      = '.conn-testimonials__stars';

    constructor(private readonly el: HTMLElement) {
        this.init();
    }

    private init(): void {
        this.renderStars();

        const layout = this.el.dataset.layout || 'carousel';

        if (layout === 'carousel') {
            this.initCarousel();
        } else {
            this.initGrid();
        }
    }

    // Render star icons from data-rating="4" attribute
    private renderStars(): void {
        this.el.querySelectorAll<HTMLElement>(Testimonials.STARS_SEL).forEach((starsEl) => {
            const rating = parseInt(starsEl.dataset.rating || '5', 10);
            starsEl.innerHTML = Array.from({ length: 5 }, (_, i) =>
                `<span aria-hidden="true">${i < rating ? '★' : '☆'}</span>`,
            ).join('');
            starsEl.setAttribute('aria-label', `${rating} out of 5 stars`);
        });
    }

    private initCarousel(): void {
        const swiperEl = this.el.querySelector<HTMLElement>(Testimonials.SWIPER_SEL);
        if (!swiperEl) return;

        const ap      = readAutoplay(this.el);
        const columns = parseInt(this.el.dataset.columns || '2', 10);
        // Per device (settings.columns "desktop,tablet,mobile"); 0 = automatic.
        const tablet  = parseInt(this.el.dataset.columnsTablet || '0', 10) || Math.min(columns, 2);
        const mobile  = parseInt(this.el.dataset.columnsMobile || '0', 10) || 1;
        const withNav = this.el.dataset.navigation === ENABLED_VALUE;
        const withPag = this.el.dataset.pagination !== 'false';

        const modules = [];
        if (withNav) modules.push(Navigation);
        if (withPag) modules.push(Pagination);
        if (ap.enabled) modules.push(Autoplay);

        new Swiper(swiperEl, {
            modules,
            loop: true,
            speed: 500,
            grabCursor: true,
            slidesPerView: mobile,
            spaceBetween: 16,

            breakpoints: {
                [TABLET_MIN]: {
                    slidesPerView: tablet,
                    spaceBetween: 20,
                },
                [DESKTOP_MIN]: {
                    slidesPerView: columns,
                    spaceBetween: 24,
                },
            },

            navigation: withNav ? {
                prevEl: this.el.querySelector(Testimonials.BTN_PREV_SEL),
                nextEl: this.el.querySelector(Testimonials.BTN_NEXT_SEL),
            } : false,

            pagination: withPag ? {
                el: this.el.querySelector(Testimonials.PAGINATION_SEL),
                clickable: true,
            } : false,

            autoplay: buildSwiperAutoplay(ap.enabled, ap.delay),

            a11y: {
                prevSlideMessage: 'Previous testimonial',
                nextSlideMessage: 'Next testimonial',
            },
        });
    }

    private initGrid(): void {
        const cards = this.el.querySelectorAll(Testimonials.CARD_SEL);
        if (!cards.length) return;

        gsap.from(cards, {
            opacity: 0,
            duration: 0.4,
            stagger: 0.08,
            ease: 'power2.out',
            scrollTrigger: {
                trigger: this.el,
                start: 'top 80%',
                once: true,
            },
        });
    }

}

registry.register('.conn-testimonials', el => new Testimonials(el));
