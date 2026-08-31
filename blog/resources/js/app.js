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
