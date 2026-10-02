# jplevi.com — what this is, and why the blog exists

A short account of what the site is for and what job each part of it does. The
build details live in [README.md](README.md); the blog's decisions and phases
live in [BLOG-ROADMAP.md](BLOG-ROADMAP.md) and
[BLOG-PHASE-TWO.md](BLOG-PHASE-TWO.md). This is the why.

---

## The company

**JP LEVI INC.** is a New Jersey corporation, incorporated in Newark on
28 August 2015 and now based in North Brunswick. **JP Levi AI** is its business
side: AI and machine learning engineering for companies that have a real
problem and no appetite for a science project.

The work is described on the site as four things, in this order, because that
is the order they actually happen in: **Strategy, Engineering, Deployment,
Support.** The last one is the part most vendors leave out.

What gets built:

| | |
|---|---|
| **Private knowledge systems** | RAG and GraphRAG over a company's own documents, with citations back to the source. The honest version of "a chatbot for our files". |
| **MCP servers and connectors** | A company's systems exposed as tools any model can call — built once against the protocol, usable from Claude, an IDE, or their own application. |
| **Predictive ML** | Forecasting, classification, scoring. Models that answer a question someone is currently guessing at. |
| **Business platforms** | Full-stack products. The software around the model, which is usually most of the work. |
| **Managed deployment** | Hosting and operating what we build, through a partner, so nobody is handed a repository and wished luck. |

There is a gaming side to the company too. It lives in `app/(gaming)/` and is
kept deliberately separate — different audience, different voice, its own
front door. Nothing in the business nav points at it.

## Who the site is talking to

Two audiences, and they want opposite things.

**Owners and operators** arrive with a problem in their own words: the same
details live in five systems, customers ask the same five questions, nobody can
find anything in the files. They are not shopping for a model architecture.
They want to know whether this is fixable, roughly what it costs, and whether
the person on the other end will still be there in six months.

**Engineers** arrive to find out whether we know what we are doing. They read
the technical detail, and they notice when it is vague.

Almost every page is written for both. The capability sections carry a plain
sentence and a technical one; posts are tagged for buyers, engineers, or both.
The one thing the site never does is tell an owner that AI is the answer when
it is not — several of the case notes exist specifically to say the opposite.

## What the site is made of

Two applications on one domain, which is a deliberate choice rather than an
accident of history.

```
jplevi.com/           Next.js 14, static export, no server
jplevi.com/blog/      Laravel 12 + Filament + MySQL, PHP 8.3
```

The marketing site is **static**. Every page is HTML on disk before a visitor
arrives, so there is nothing to be slow, nothing to patch, and nothing to
compromise. It deploys by pushing to `main`.

The blog is a **real application**, because a blog needs a database, an editor,
comments and a subscriber list, and pretending otherwise leads to a worse
version of all four. It is a self-contained add-on: the static site gained one
nav link — **Notes** — and nothing else. Neither deploy touches the other.

---

## Why there is a blog at all

### It is the portfolio

The hardest thing to prove in this business is that you can actually build
things. Anyone can list RAG, MCP and MLOps on a services page. A body of
written work about real projects — with the numbers, the mistakes, and the
things that did not work — is evidence in a way that a capabilities list is
not.

The blog documents Kaggle projects, research, side projects, and opinion pieces
on where AI genuinely helps a business. Including the pieces that conclude it
does not.

### Building it *is* part of the argument

This was the deciding question when the blog was planned, and it is worth
writing down, because the easy answer was WordPress.

Installing WordPress would have taken an afternoon. It would also have said,
on the site of a company that sells software engineering, that the company
reaches for someone else's software when it has a problem of its own. The blog
is running proof of the same skills the services page claims: a real schema, a
role system, a custom editor, an image pipeline, a page cache, a comment system
with moderation, a newsletter with proper unsubscribe handling.

So the admin panel looks like WordPress on purpose — that is muscle memory
worth keeping — but none of it is WordPress. The parts that are genuinely not
differentiators are rented rather than built: email delivery, social posting.
Everything a reader or a search engine touches is ours.

### It compounds where a services page does not

The services pages answer people who already found us. The blog is how people
find us in the first place, and it keeps working long after it is written.

It sits at `jplevi.com/blog` rather than `blog.jplevi.com` for exactly this
reason: a subdirectory inherits the domain's authority in search, a subdomain
starts from zero.

### Engineers need collaborators, not just clients

Not everything here is aimed at a buyer. Some of it is written for other
engineers — because interesting work comes from other people who do this, and
that only happens if they can see what you think.

---

## What the blog can do

| | |
|---|---|
| **Writing** | A block editor: callouts, pull quotes, galleries, tabs, accordions, video embeds, and raw HTML for anything else. Revisions on every save. |
| **Images** | One upload becomes four widths plus a 1200×630 share crop. WebP for the page, JPEG for link previews, because scraper support for WebP is still uneven. |
| **Search visibility** | Per-post meta, canonical URLs, structured data, RSS, a sitemap, and permanent redirects whenever a slug changes. Thin category archives stay out of the index until they are worth landing on. |
| **Sharing** | Full Open Graph and Twitter card metadata, and an automatic post to social when an article goes live. |
| **Readers** | Comments with sign-in through Google, GitHub or LinkedIn, moderation, and a newsletter with double opt-in and one-click unsubscribe. |
| **Analytics** | Our own page-view counting, aggregated by day. No third-party tracking of any kind, which is the only way the privacy page stays honest. |

Some of it is wired and inert until credentials are set: the newsletter needs a
Resend key plus SPF, DKIM and DMARC records; reader sign-in needs OAuth
credentials; automatic social posting needs an API key. The code is built and
tested in every case — see the roadmaps for the current state.

---

## The principles underneath

A few decisions repeat across both applications, and they explain most of the
code:

**Nothing tracks anyone.** No analytics scripts, no Gravatar, no fonts that
report back beyond Google Fonts. Author avatars are initials on a coloured disc
unless a real photograph has been uploaded. This is a real constraint, not a
preference — it is what lets the privacy page say something true.

**A reader's page costs a reader nothing.** The staff toolbar is not rendered
for anyone who is not signed in. Maths and syntax highlighting load only on
pages that contain maths or code. Anonymous pages are served from a cache.

**Say what went wrong.** An upload that fails says why. A refused image says
what size would have worked. Failure messages are written for the person who
has to fix it.

**Own the things people judge us on.** The front end, the editor, the content
model, the design system. Rent the things nobody chooses a vendor over.
