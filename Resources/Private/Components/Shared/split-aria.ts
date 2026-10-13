/*
 * This file is part of the Connetation TYPO3 Web Toolkit - T3WTK.
 *
 * Copyright (c) Connetation Web Engineering GmbH
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
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

/**
 * aria option for GSAP SplitText.
 *
 * SplitText's default ("auto") moves the text into an aria-label on the split
 * element and hides the generated spans. aria-label is prohibited on elements
 * with a generic/paragraph role (p, div, span …), which fails axe/Lighthouse
 * ("aria-prohibited-attr"). Those keep their spans readable instead ("none");
 * headings and elements with an explicit role may carry a label.
 */
export function splitAria(el: HTMLElement): 'auto' | 'none' {
    return el.matches('h1, h2, h3, h4, h5, h6, [role]') ? 'auto' : 'none';
}
