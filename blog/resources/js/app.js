import katex from 'katex';
import renderMathInElement from 'katex/contrib/auto-render';
import hljs from 'highlight.js/lib/common';

/*
 * Maths and code highlighting.
 *
 * Both are bundled rather than pulled from a CDN, because the content security
 * policy only allows script and styles from this origin. That also means a post
 * renders the same whether or not a third party is up.
 */
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.prose-jp pre code').forEach((block) => {
        hljs.highlightElement(block);
    });

    const article = document.querySelector('.prose-jp');
    if (!article) return;

    renderMathInElement(article, {
        delimiters: [
            { left: '$$', right: '$$', display: true },
            { left: '\\[', right: '\\]', display: true },
            { left: '$', right: '$', display: false },
            { left: '\\(', right: '\\)', display: false },
        ],
        // A dollar sign in prose must not silently become maths.
        ignoredTags: ['script', 'noscript', 'style', 'textarea', 'option'],
        throwOnError: false,
    });
});

/*
 * Tabs.
 *
 * Delegated from the document and driven by data attributes, because the
 * content security policy forbids inline handlers and that policy is what keeps
 * an escaping mistake in the comments from becoming somebody else's script.
 * If this file never loads, every panel is simply visible: uglier, still
 * readable, nothing lost.
 */
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
