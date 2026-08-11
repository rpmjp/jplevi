import Link from "next/link";
import ReactMarkdown, { type Components } from "react-markdown";
import remarkGfm from "remark-gfm";
import rehypeRaw from "rehype-raw";

/**
 * Renders markdown verbatim. No sanitising, rewriting, or reflowing of the
 * source text - the legal copy is rendered exactly as authored.
 *
 * rehype-raw passes through inline HTML in the source, so authored markup
 * renders as markup and `<!-- comments -->` stay invisible instead of being
 * escaped into visible page text. These documents are repo-controlled files
 * read at build time, never user input, so raw HTML is safe here.
 */
const components: Components = {
  // Wide tables scroll inside their own container so the page body never does.
  table: ({ children, ...props }) => (
    <div className="my-8 -mx-1 overflow-x-auto border-2 border-mecha-line px-1">
      <table {...props}>{children}</table>
    </div>
  ),
  // Long code blocks get the same treatment.
  pre: ({ children, ...props }) => (
    <pre {...props} className="overflow-x-auto border-2 border-mecha-line">
      {children}
    </pre>
  ),
  a: ({ children, href, node, ...props }) => {
    const isExternal = Boolean(href && /^https?:\/\//i.test(href));
    if (isExternal) {
      return (
        <a href={href} rel="noopener noreferrer" target="_blank" {...props}>
          {children}
        </a>
      );
    }

    // Mail and anchor links pass through untouched.
    if (!href || !href.startsWith("/")) {
      return (
        <a href={href} {...props}>
          {children}
        </a>
      );
    }

    // The site is exported with trailingSlash: true. Documents authored with
    // bare internal paths (/mechablast/terms) would otherwise cost a redirect,
    // so normalise the href here rather than editing the source text.
    const [path, rest = ""] = href.split(/(?=[?#])/);
    const normalised = path.endsWith("/") ? path : `${path}/`;
    return (
      <Link href={`${normalised}${rest}`} {...props}>
        {children}
      </Link>
    );
  },
};

export function Markdown({ children }: { children: string }) {
  return (
    <ReactMarkdown
      remarkPlugins={[remarkGfm]}
      rehypePlugins={[rehypeRaw]}
      components={components}
    >
      {children}
    </ReactMarkdown>
  );
}
