/**
 * Connetation Elements – People
 */

class People {

    private static readonly INTERACTIVE_SEL = [
        '.conn-people__flip-card',
        '.conn-people__team-flip',
        '.conn-people__reveal--sheet',
        '.conn-people__reveal--curtain',
        '.conn-people__reveal--spotlight',
    ].join(', ');

    constructor(private readonly el: HTMLElement) {
        this.init();
    }

    private init(): void {
        if (window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
            return;
        }

        this.el.querySelectorAll<HTMLElement>(People.INTERACTIVE_SEL).forEach(card => {
            card.addEventListener('click', event => {
                if ((event.target as HTMLElement).closest('a')) {
                    return;
                }
                card.classList.toggle('is-flipped');
            });
        });
    }

}

import { registry } from '../Shared/registry';
registry.register('.conn-people', el => new People(el));
