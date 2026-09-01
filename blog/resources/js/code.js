import hljs from 'highlight.js/lib/common';
import 'highlight.js/styles/github-dark.min.css';

/* Same reasoning as the maths module: a post with no code block never pays for
   the highlighter. */
export default function highlight(blocks) {
    blocks.forEach((block) => hljs.highlightElement(block));
}
