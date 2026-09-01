import renderMathInElement from 'katex/contrib/auto-render';
import 'katex/dist/katex.min.css';

/* Imported dynamically, so KaTeX and its stylesheet are fetched only by a post
   that actually contains maths. */
export default function renderMath(article) {
    renderMathInElement(article, {
        delimiters: [
            { left: '$$', right: '$$', display: true },
            { left: '\\[', right: '\\]', display: true },
            { left: '$', right: '$', display: false },
            { left: '\\(', right: '\\)', display: false },
        ],
        ignoredTags: ['script', 'noscript', 'style', 'textarea', 'option'],
        throwOnError: false,
    });
}
