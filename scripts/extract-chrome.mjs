/**
 * Extracts the site's chrome from the built static export into Blade partials.
 *
 * The header and left rail are React components compiled by Next, so the
 * Laravel app cannot import them. Copying class names by hand is what put the
 * blog on the wrong body font and made the header jump between pages. Taking
 * the rendered markup means the two cannot drift.
 *
 * Run after `npm run build`.
 */
import { readFileSync, writeFileSync, mkdirSync } from 'node:fs';

const SOURCE = 'out/services/index.html';
const OUT_DIR = 'blog/resources/views/partials';
const html = readFileSync(SOURCE, 'utf8');

/** Walks forward from an opening tag, counting depth, and returns the element. */
function extract(source, startIndex, tag) {
  if (startIndex < 0) throw new Error(`Could not find <${tag}> in ${SOURCE}`);

  const token = new RegExp(`<${tag}\\b|</${tag}>`, 'g');
  token.lastIndex = startIndex;

  let depth = 0;
  let match;

  while ((match = token.exec(source)) !== null) {
    depth += match[0].startsWith('</') ? -1 : 1;
    if (depth === 0) return source.slice(startIndex, match.index + match[0].length);
  }

  throw new Error(`Unbalanced <${tag}>`);
}

const header = extract(html, html.search(/<header\b/), 'header');
const rail = extract(html, html.search(/<div class="pointer-events-none fixed inset-y-0 left-0/), 'div');

const clean = (s) =>
  s.replace(/ data-[a-z-]+="[^"]*"/g, '').replace(/<!--\$-->|<!--\/\$-->|<!---->/g, '').trim();

/**
 * The source page is /services/, so that entry is the one marked current.
 * Move the marking to Notes, and add the session controls the static site has
 * no use for. Everything else is left exactly as Next rendered it.
 */
function forBlog(markup) {
  const ACTIVE = 'text-ink-ink after:scale-x-100';
  const INACTIVE = 'text-ink-body after:scale-x-0 hover:after:scale-x-100';

  return markup
    // Services stops being current
    .replace('<a aria-current="page" class="relative py-2', '<a class="relative py-2')
    .replace(ACTIVE + '" href="/services/"', INACTIVE + '" href="/services/"')
    // Notes becomes current
    .replace(INACTIVE + '" href="/blog/"', ACTIVE + '" href="/blog/"')
    .replace('<a class="relative py-2 font-sans text-[0.92rem] transition-colors after:absolute after:inset-x-0 after:-bottom-0.5 after:h-px after:origin-left after:bg-brand after:transition-transform hover:text-brand ' + ACTIVE + '" href="/blog/"',
             '<a aria-current="page" class="relative py-2 font-sans text-[0.92rem] transition-colors after:absolute after:inset-x-0 after:-bottom-0.5 after:h-px after:origin-left after:bg-brand after:transition-transform hover:text-brand ' + ACTIVE + '" href="/blog/"')
    // session controls sit ahead of the phone number, inside the existing cluster
    .replace('<div class="col-start-3 row-start-1 ml-auto flex items-center gap-x-6"><a href="tel:',
             '<div class="col-start-3 row-start-1 ml-auto flex items-center gap-x-6">@include(\'partials.session\')<a href="tel:');
}

mkdirSync(OUT_DIR, { recursive: true });

const banner = (what) =>
  `{{--\n    GENERATED. Do not edit.\n\n    Extracted from ${SOURCE} by scripts/extract-chrome.mjs, so the blog's\n    ${what} is the same markup the rest of the site renders. Change the React\n    component and rerun the script.\n--}}\n`;

writeFileSync(`${OUT_DIR}/site-header.blade.php`, banner('header') + forBlog(clean(header)) + '\n');
writeFileSync(`${OUT_DIR}/site-rail.blade.php`, banner('rail') + clean(rail) + '\n');

console.log('header:', clean(header).length, 'chars');
console.log('rail:  ', clean(rail).length, 'chars');
