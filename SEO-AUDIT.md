# SEO audit — jplevi.com

Audited 5 September 2026 against the live site, not the source. Every finding
below was measured; the command that produced each one is shown so it can be
re-run after a fix.

Scope: the static marketing site (`/`) and the Laravel blog (`/blog/`).

---

## The short version

The blog is in good shape. The **marketing site is invisible to structured
search**: it has no `robots.txt`, no sitemap, no structured data of any kind,
and no share image on any page. Those four gaps are worth more than everything
else in this document combined, and all four are a few hours' work.

| | |
|---|---|
| **Already right** | Titles, canonicals, one `h1` per page, clean heading order, HTTPS, trailing-slash redirects, image alt text, page weight (5–17 KB gzipped), blog metadata and article structured data |
| **Critical** | No `robots.txt`, no sitemap for the main site, no structured data, no `og:image` |
| **High** | `www` serves a second copy of the site, author archives indexable and keyed by database ID, blog canonical does not match the served URL |
| **Worth doing** | `BreadcrumbList`, `Person` for E-E-A-T, contextual links from services into posts |
| **Do not bother** | `llms.txt`, FAQ and HowTo schema — both dead in 2026, see the last section |

---

## Critical

### 1. `robots.txt` does not exist

```
$ curl -s -o /dev/null -w "%{http_code}" https://jplevi.com/robots.txt
404
```

The request falls through to the 404 page. Nothing is blocked and nothing is
broken by this on its own — crawlers assume "allow all" when the file is
missing — but it is where a sitemap is declared, and there is currently nowhere
to declare one.

**Fix.** Add `public/robots.txt`, which the static export copies to the web root
verbatim. Declare both sitemaps, and keep the admin panel out of the index:

```
User-agent: *
Allow: /
Disallow: /blog/admin/

Sitemap: https://jplevi.com/sitemap.xml
Sitemap: https://jplevi.com/blog/sitemap.xml
```

### 2. The marketing site has no sitemap

```
$ curl -s -o /dev/null -w "%{http_code}" https://jplevi.com/sitemap.xml
404
```

The blog has one (`/blog/sitemap.xml`, three URLs, with `lastmod`). The six
pages that describe the business have none. They are discoverable by crawling
links, so they are not invisible — but nothing tells Google when a page last
changed, which is the signal that gets a page recrawled after an edit.

**Fix.** Next 14 generates this: add `app/sitemap.ts` exporting the six routes
with `lastModified`. It lands in `out/sitemap.xml` at build time, so it deploys
with everything else and never goes stale.

### 3. No structured data anywhere on the marketing site

```
$ curl -s https://jplevi.com/ | grep -c 'application/ld+json'
0        # and 0 on /services/, /hosting/, /about/, /contact/, /gaming/
```

Blog posts carry a full `BlogPosting`. The pages that describe an actual New
Jersey company carry nothing. This is the largest single gap in the audit, for
two reasons.

It is how Google learns the company is an entity — name, address, phone,
founding date, the person behind it — rather than six pages of text that
mention a company. [`Organization` and `LocalBusiness` remain among the schema
types that still earn rich results in 2026](https://www.digitalapplied.com/blog/structured-data-seo-2026-rich-results-guide),
after the cull that took FAQ and HowTo.

It is also the machine-readable version of the facts an AI answer needs to cite
you. Perplexity and AI Overviews [retrieve pages at query time](https://www.leapd.ai/blog/ai-visibility/how-chatgpt-google-ai-overviews-and-perplexity-source-information-in-2026),
so the same work that helps Google helps them.

**Fix, in priority order.**

1. **`Organization`** in the root layout, on every page. Legal name, the 2015
   founding date, `areaServed`, `sameAs` pointing at whatever profiles you are
   willing to link, `contactPoint` with the phone and email already on the site.
2. **`LocalBusiness`** (or `ProfessionalService`) on `/about/`, with the North
   Brunswick address. This is what puts a business into local results at all.
   It needs a real address to be worth doing — decide whether you want the
   street address public before this one.
3. **`Person`** for Robert on `/about/`, with `alumniOf` for NJIT and Rutgers
   and `knowsAbout` for the specialisms. Google's guidance on experience and
   expertise leans on knowing who is behind the work.
4. **`Service`** on `/services/`, one per capability, each with `serviceType`
   and `provider` pointing back at the Organization.
5. **`WebSite`** with `potentialAction` for the blog's search box.

Everything above is data the site already states in prose. None of it is a
claim you are not already making.

### 4. No share image on any page of the marketing site

```
$ curl -s https://jplevi.com/ | grep -c 'og:image'
0        # same on every static page
```

Every share of the homepage, the services page or the contact page on LinkedIn,
Slack, iMessage or X arrives as a bare blue link. The blog has this right — a
1200×630 JPEG per post — and the marketing site, which is the part people
actually send to each other when recommending you, has nothing.

**Fix.** `public/hero.png` already exists. At minimum, set a site-wide default
in the root layout's `openGraph.images`. Better: one image per page, since
"Services" and "Contact" deserve different cards. The blog's `ImageIngest`
already produces exactly this crop — the same 1200×630 JPEG rule applies, and
[JPEG rather than WebP, because scraper support for WebP is still
uneven](https://darekkay.com/blog/open-graph-image-formats/).

---

## High

### 5. `www.jplevi.com` serves a second copy of the site

```
$ curl -s -o /dev/null -w "%{http_code}" https://www.jplevi.com/
200        # not redirected
```

Two hostnames serving identical content. The damage is limited because the
canonical tag on the `www` copy correctly points at the bare domain:

```
$ curl -s https://www.jplevi.com/ | grep -oE 'rel="canonical" href="[^"]*"'
rel="canonical" href="https://jplevi.com/"
```

So Google will almost certainly consolidate. But a canonical is a hint and a
301 is an instruction, and link equity from anyone who links to the `www` form
consolidates more reliably through a redirect.

**Fix.** A redirect in the root `.htaccess`, above the existing rules.

The same applies to `https://jplevi.com/index.html`, which also returns 200 and
also canonicals correctly. Lower priority — nobody links to it — but it is the
same class of thing.

### 6. Author archives are indexable, thin, and keyed by database ID

```
$ curl -s -o /dev/null -w "%{http_code}" https://jplevi.com/blog/by/1
200        # no robots directive at all
```

Topic and tag archives are correctly `noindex, follow`. The author archive is
not, and on a blog with one author it is a duplicate of the index with a
different heading. It is also addressed by the primary key, which is both an
unattractive URL and a needless disclosure of how many accounts exist.

**Fix.** Two small changes: `noindex, follow` on the author archive on the same
reasoning as the others, and route-model-bind the user by a slug rather than by
`id`. If it stays indexable it should at least be `/blog/by/robert-jean-pierre`.

### 7. The blog index canonical does not match the URL it is served at

```
$ curl -s https://jplevi.com/blog/ | grep -oE 'rel="canonical" href="[^"]*"'
rel="canonical" href="https://jplevi.com/blog"     # served at /blog/
```

A self-referencing canonical that points somewhere slightly different from the
page it sits on. `/blog` 301s to `/blog/`, so this resolves — but it makes the
canonical a redirect hop instead of a direct statement.

**Fix.** One line: make the canonical match the served form, trailing slash
included. This is a consequence of the mount quirk documented in
`routes/web.php` — the home page is the one URL where the base path is not
stripped.

---

## Worth doing

### 8. `BreadcrumbList` on the blog

Still one of the schema types that earns a visible rich result. Posts sit under
categories already; the trail exists in the design and is not marked up.

### 9. Nothing links from the services pages into the writing

The blog links back into the site — the footer carries Services, Hosting,
Company and Contact on every page. Nothing goes the other way except the
"Notes" nav item.

A post proving you can do the thing a services page claims is the strongest
internal link on the site, in both directions. It passes authority to the
service page and gives the reader evidence at the moment they are deciding.

**Fix.** In each capability block on `/services/`, link the one or two posts
that demonstrate it. This is editorial rather than technical, and it is the
highest-value item in this section.

### 10. Publishing cadence, for the AI surfaces specifically

Perplexity weights recency heavily — [content under 30 days old is cited
roughly three times as often](https://seosherpa.com/ai-search-statistics/).
A blog that publishes something monthly is treated very differently from one
that published three things in a week and then stopped. Two posts a month beats
eight in a fortnight and then nothing.

---

## Do not bother

Two pieces of advice you will be given repeatedly that are wrong as of 2026.
Both cost real time.

**`llms.txt` does nothing.** Google's May 2026 AI optimisation guidance
[explicitly states it is not needed for AI Overviews, AI Mode, or any other
generative feature](https://www.digitalapplied.com/blog/google-llms-txt-no-seo-value-lighthouse-audit-2026),
and an analysis of 137,000 domains found [97% of `llms.txt` files received zero
requests](https://www.getpassionfruit.com/blog/should-i-create-an-llms.txt-file-google-s-2026-guidance-explained).
No major provider has committed to reading it in production. It is a file that
makes a site feel modern and is fetched by nothing.

**FAQ and HowTo schema no longer produce rich results.** [FAQ rich results were
removed on 7 May 2026](https://www.getpassionfruit.com/blog/what-changed-with-google-drops-faq-rich-results-and-what-to-do-now);
HowTo went in September 2023. Neither markup is penalised, and neither will show
you anything. Write the FAQ if readers need it; do not mark it up expecting a
result.

---

## Suggested order

Roughly by value per hour of work.

| | Item | Where |
|---|---|---|
| 1 | `robots.txt` + `app/sitemap.ts` | Static site |
| 2 | `Organization` in the root layout | Static site |
| 3 | Default `og:image`, then per-page | Static site |
| 4 | `www` → apex 301 | `.htaccess` |
| 5 | `LocalBusiness` + `Person` on `/about/` | Static site |
| 6 | `noindex` and a slug on author archives | Blog |
| 7 | Blog index canonical trailing slash | Blog |
| 8 | `Service` on `/services/` | Static site |
| 9 | Links from services into posts | Editorial |
| 10 | `BreadcrumbList` | Blog |

Items 1–4 are mechanical and I can do them in one pass. Item 5 needs a decision
from you about publishing the street address. Item 9 needs your judgement about
which post backs which service.
