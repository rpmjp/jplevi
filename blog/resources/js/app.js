/*
 * Everything here is either tiny or loaded on demand.
 *
 * KaTeX and highlight.js are together about 400KB, and most posts contain
 * neither maths nor code. Importing them dynamically means a reader downloads
 * them only on a page that needs them, which on a content site is the
 * difference between a fast page and a slow one.
 */

/* Tabs. Delegated from the document and driven by data attributes, because the
   content security policy forbids inline handlers and that policy is what keeps
   an escaping mistake in the comments from becoming somebody else's script. If
   this file never loads, every panel is simply visible. */
document.addEventListener('click', (event) => {
    const trigger = event.target.closest('[data-tab-target]');
    if (!trigger) return;

    const group = trigger.closest('[data-tabs]');
    if (!group) return;

    group.querySelectorAll('[data-tab-target]').forEach((tab) => {
        const selected = tab === trigger;
        tab.setAttribute('aria-selected', selected ? 'true' : 'false');
        tab.classList.toggle('border-brand', selected);
        tab.classList.toggle('text-ink-ink', selected);
        tab.classList.toggle('border-transparent', !selected);
        tab.classList.toggle('text-ink-soft', !selected);
    });

    group.querySelectorAll('[data-tab-panel]').forEach((panel) => {
        panel.hidden = panel.id !== trigger.dataset.tabTarget;
    });
});

/* Arrow keys move between tabs, as a tablist is expected to. */
document.addEventListener('keydown', (event) => {
    if (!['ArrowLeft', 'ArrowRight'].includes(event.key)) return;

    const current = document.activeElement?.closest('[data-tab-target]');
    if (!current) return;

    const tabs = [...current.closest('[data-tabs]').querySelectorAll('[data-tab-target]')];
    const next = tabs[(tabs.indexOf(current) + (event.key === 'ArrowRight' ? 1 : -1) + tabs.length) % tabs.length];

    next.focus();
    next.click();
});

document.addEventListener('DOMContentLoaded', () => {
    const article = document.querySelector('.prose-jp');
    if (!article) return;

    const blocks = article.querySelectorAll('pre code');
    if (blocks.length) {
        import('./code.js').then((m) => m.default(blocks));
    }

    /* Cheap test before a 260KB download. Deliberately stricter than a bare
       dollar sign: a post that mentions $500 has no maths in it, and would
       otherwise pay for the whole typesetter to find that out. A pair is
       required, on one line, with something between them. */
    const hasMath = /\$\$[^]+?\$\$|\$[^$\n]+\$|\\\([^]+?\\\)|\\\[[^]+?\\\]/;

    if (hasMath.test(article.textContent)) {
        import('./math.js').then((m) => m.default(article));
    }
});
