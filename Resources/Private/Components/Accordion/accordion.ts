/**
 * Conn Bootstrap Accordion
 * Core open/close behaviour is handled natively by Bootstrap 5 via
 * data-bs-toggle / data-bs-target attributes — no extra code needed.
 *
 * This module adds optional session-state persistence: panels that were
 * open when the user navigates away will still be open on return.
 * State persistence is skipped for mutual-exclusion accordions
 * (those using data-bs-parent), because only one panel is ever open.
 */

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[id^="conn-accordion-"]').forEach(el => initAccordion(el as HTMLElement));
});

function initAccordion(accordion: HTMLElement): void {
    // Mutual-exclusion accordions (data-bs-parent present) are self-managing.
    const hasMutualExclusion = accordion.querySelector('[data-bs-parent]') !== null;
    if (hasMutualExclusion) return;

    const storageKey = `conn-acc:${accordion.id}`;

    // Restore previously open panels
    try {
        const saved = JSON.parse(sessionStorage.getItem(storageKey) ?? '[]') as string[];
        saved.forEach(panelId => {
            const panel = accordion.querySelector(`#${panelId}`);
            const btn   = accordion.querySelector(`[data-bs-target="#${panelId}"]`);
            if (panel && !panel.classList.contains('show')) {
                panel.classList.add('show');
                btn?.classList.remove('collapsed');
                btn?.setAttribute('aria-expanded', 'true');
            }
        });
    } catch { /* ignore malformed sessionStorage data */ }

    // Persist state whenever a panel opens or closes
    accordion.addEventListener('shown.bs.collapse',  () => persistState(accordion, storageKey));
    accordion.addEventListener('hidden.bs.collapse', () => persistState(accordion, storageKey));
}

function persistState(accordion: HTMLElement, storageKey: string): void {
    const openIds = [...accordion.querySelectorAll('.accordion-collapse.show')]
        .map(el => el.id);
    sessionStorage.setItem(storageKey, JSON.stringify(openIds));
}
