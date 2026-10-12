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
 * Conn Process/Steps
 *
 * Alternates row direction (row 1 left-to-right, row 2 right-to-left, row 3
 * left-to-right, ...) so a row transition always lands on the same screen
 * edge (last item of one row, first item of the reversed next row) — the
 * classic "boustrophedon" step-process look, matching a hand-drawn zigzag
 * rather than a diagonal cutting back across the whole layout.
 *
 * Row direction is reversed visually only, via the CSS `order` property —
 * DOM order (and therefore reading/tab order for screen readers and
 * keyboard users) stays 1..N throughout. Row membership is detected by
 * measuring each step's actual position (flex-wrap decides that at runtime
 * depending on container width), so this stays correct at every breakpoint.
 *
 * Each row transition is routed as a rounded rectangle through the OUTER
 * gutter — beyond the widest point of every row's actual content (marker +
 * text), not just the departing row's — so the vertical leg of the path
 * never crosses any step's text, regardless of how many rows sit between
 * the transition's start and end.
 */

const SVG_NS = 'http://www.w3.org/2000/svg';
const ROW_TOLERANCE = 4;
const GUTTER = 24;
const CORNER_RADIUS = 20;

interface Point {
    x: number;
    y: number;
}

class Process {

    private readonly list: HTMLElement | null;
    private readonly svg: SVGSVGElement | null;
    private resizeTimer = 0;
    private resizeObserver: ResizeObserver | null = null;

    constructor(private readonly el: HTMLElement) {
        this.list = el.querySelector('.conn-process__list');
        this.svg = el.querySelector('.conn-process__connectors');
        this.init();
    }

    private init(): void {
        if (!this.list) return;

        this.layoutAndDraw();

        this.resizeObserver = new ResizeObserver(() => this.debouncedLayout());
        this.resizeObserver.observe(this.list);
        window.addEventListener('resize', () => this.debouncedLayout());
    }

    private debouncedLayout(): void {
        window.clearTimeout(this.resizeTimer);
        this.resizeTimer = window.setTimeout(() => this.layoutAndDraw(), 100);
    }

    private layoutAndDraw(): void {
        if (!this.list) return;

        const steps = Array.from(this.list.querySelectorAll<HTMLElement>('.conn-process__step'));
        if (steps.length < 2) return;

        // Reset any previous visual reordering before re-measuring — otherwise
        // a resize would group rows based on an already-reordered layout.
        steps.forEach(step => { step.style.order = ''; });

        const rows = this.groupIntoRows(steps);
        this.applySerpentineOrder(rows);

        if (this.svg) {
            this.draw(rows);
        }
    }

    /** Groups steps by matching top position — i.e. by the row flex-wrap put them in. */
    private groupIntoRows(steps: HTMLElement[]): HTMLElement[][] {
        const rows: HTMLElement[][] = [];
        let lastTop: number | null = null;

        for (const step of steps) {
            const top = step.getBoundingClientRect().top;
            if (lastTop === null || Math.abs(top - lastTop) > ROW_TOLERANCE) {
                rows.push([]);
                lastTop = top;
            }
            rows[rows.length - 1].push(step);
        }

        return rows;
    }

    /** Every odd row (0-indexed: the 2nd, 4th, ...) renders right-to-left instead of left-to-right. */
    private applySerpentineOrder(rows: HTMLElement[][]): void {
        let order = 0;
        for (const [rowIndex, row] of rows.entries()) {
            const visualRow = rowIndex % 2 === 1 ? [...row].reverse() : row;
            for (const step of visualRow) {
                step.style.order = String(order++);
            }
        }
    }

    private draw(rows: HTMLElement[][]): void {
        if (!this.list || !this.svg) return;

        const listRect = this.list.getBoundingClientRect();
        this.svg.setAttribute('width', String(listRect.width));
        this.svg.setAttribute('height', String(listRect.height));
        this.svg.setAttribute('viewBox', `0 0 ${listRect.width} ${listRect.height}`);
        this.svg.innerHTML = '';

        // Re-read marker positions AFTER serpentine reordering — querySelectorAll
        // still returns DOM order, but each element's on-screen position now
        // reflects its (possibly reversed) visual row order.
        const markers = Array.from(this.list.querySelectorAll<HTMLElement>('.conn-process__marker'));
        const points: Point[] = markers.map(marker => {
            const rect = marker.getBoundingClientRect();
            return {
                x: rect.left + rect.width / 2 - listRect.left,
                y: rect.top + rect.height / 2 - listRect.top,
            };
        });

        // The widest extent of ALL rows' content (marker + text), not just
        // the departing row's — a transition's vertical leg has to clear
        // every row it passes alongside, not only the one it's leaving.
        const allSteps = rows.flat();
        const maxRight = Math.max(...allSteps.map(step => step.getBoundingClientRect().right)) - listRect.left;
        const minLeft = Math.min(...allSteps.map(step => step.getBoundingClientRect().left)) - listRect.left;

        // Row-transition point indices: where one row ends and the next begins.
        // Every row transition gets this treatment — including when every row
        // holds exactly one item (a narrow screen collapsing the layout into
        // a single column): each item is still technically its own "row", and
        // the alternating bulge below turns that into a deliberate zigzag
        // down the column rather than a plain straight line.
        const transitionsAt = new Set<number>();
        let cursor = 0;
        for (const row of rows.slice(0, -1)) {
            cursor += row.length;
            transitionsAt.add(cursor - 1);
        }

        const path = document.createElementNS(SVG_NS, 'path');
        path.setAttribute('class', 'conn-process__connector-line');
        path.setAttribute('fill', 'none');
        path.setAttribute('d', this.buildPath(points, transitionsAt, minLeft, maxRight));
        this.svg.appendChild(path);
    }

    private buildPath(points: Point[], transitionsAt: Set<number>, minLeft: number, maxRight: number): string {
        const segments = [`M ${points[0].x},${points[0].y}`];
        let transitionIndex = 0;

        for (let i = 0; i < points.length - 1; i++) {
            const from = points[i];
            const to = points[i + 1];

            if (!transitionsAt.has(i)) {
                segments.push(`L ${to.x},${to.y}`);
                continue;
            }

            segments.push(this.buildTransition(from, to, minLeft, maxRight, transitionIndex));
            transitionIndex++;
        }

        return segments.join(' ');
    }

    /**
     * Rounded-rectangle "hook" from `from` out to the outer gutter, down (or
     * up) to the next row's height, then back in to `to` — three straight
     * segments with a quarter-circle fillet at each of the two corners.
     * Routing through the gutter (beyond every row's content, not just a
     * straight dip below the departing row) guarantees the vertical leg
     * never crosses any step's text.
     *
     * Bulge side alternates strictly by transition occurrence (1st right,
     * 2nd left, 3rd right, ...) rather than by comparing `from.x` to the
     * container's midpoint — that comparison degenerates the moment every
     * row has just one item (a single, centered column), where every
     * transition's `from.x` is roughly the same value and would otherwise
     * all bulge to the same side instead of zigzagging.
     */
    private buildTransition(from: Point, to: Point, minLeft: number, maxRight: number, transitionIndex: number): string {
        const bulgeRight = transitionIndex % 2 === 0;
        const edgeX = bulgeRight ? maxRight + GUTTER : minLeft - GUTTER;
        const hDir = bulgeRight ? 1 : -1; // direction of travel along the horizontal legs, toward/away from the edge
        const vDir = to.y > from.y ? 1 : -1; // direction of travel down the vertical leg

        const corner1 = { x: edgeX - hDir * CORNER_RADIUS, y: from.y };
        const corner1End = { x: edgeX, y: from.y + vDir * CORNER_RADIUS };
        const corner2 = { x: edgeX, y: to.y - vDir * CORNER_RADIUS };
        const corner2End = { x: edgeX - hDir * CORNER_RADIUS, y: to.y };

        return [
            `L ${corner1.x},${corner1.y}`,
            `Q ${edgeX},${from.y} ${corner1End.x},${corner1End.y}`,
            `L ${corner2.x},${corner2.y}`,
            `Q ${edgeX},${to.y} ${corner2End.x},${corner2End.y}`,
            `L ${to.x},${to.y}`,
        ].join(' ');
    }

}

import { registry } from '../Shared/registry';
registry.register('.conn-process', el => new Process(el));
