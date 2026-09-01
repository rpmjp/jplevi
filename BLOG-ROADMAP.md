# Blog build roadmap

> **Status: complete and deployed.** All ten phases are built and live at
> jplevi.com/blog. Kept as a record of what was decided and why. The follow up
> work is in [BLOG-PHASE-TWO.md](BLOG-PHASE-TWO.md).
>
> Still waiting on credentials rather than code: a Resend key plus SPF, DKIM and
> DMARC for the newsletter, OAuth for reader sign in, and a social posting key.
> Each is wired, tested, and inert until set.

A blog we built ourselves. Laravel on the Hostinger Business plan, at
`jplevi.com/blog`. Own admin, own editor, own comments, own subscriber list.
The static site is never touched: it gains one nav link and nothing else.

## The decisions this rests on

| | |
|---|---|
| **Where it lives** | `jplevi.com/blog`. A subdirectory inherits the domain's authority in search. A subdomain starts from zero. |
| **Stack** | Laravel 12, MySQL, PHP 8.3. Everything the Business plan already provides: SSH, Git, Composer, cron. |
| **Admin** | Filament, themed to our palette and type. WordPress muscle memory, not WordPress. |
| **Editor** | TipTap. Headless, so every control is ours to style. |

## What we build, what we rent

| Component | Decision | Reasoning |
|---|---|---|
| Auth and roles | Build | Laravel Breeze plus spatie/laravel-permission. Roles are data, not hardcoded routes. |
| Social sign-in | Build | Socialite. Google for everyone, GitHub for engineers, LinkedIn for buyers. |
| Admin panel | Build | Filament scaffolds tables and forms. The content model and theme are ours. |
| Rich text editor | Build | TipTap gives the engine. We build the toolbar, embeds, image flow. |
| Public blog | Build | Our design system, ported. This is the part people judge us on. |
| Comments | Build | Remark42 needs a Go binary and cannot run on shared hosting. |
| Analytics | Build | Own pageview logging. No third-party tracking, which keeps the colophon honest. |
| Email delivery | Rent | Resend's API. Deliverability and bounce handling are not a differentiator. |
| Social posting | Rent | One unified API instead of five OAuth flows that each break separately. |

---

## Phase 00: Groundwork

**Goal.** The deploy that publishes the main site must never be able to delete
the blog. This phase exists entirely to guarantee that.

- Create the MySQL database and a user scoped to it alone.
- Put the Laravel application *outside* `public_html`. Only a thin loader sits in the web root.
- Keep uploads and the database outside the directory the FTP action syncs.
- Add `blog/**` to the deploy action's exclude list, then push a throwaway commit and confirm the folder survives.
- Verify PHP 8.3, Composer, Git and SSH on the account.
- Set up a local environment mirroring the server's PHP version.
- Decide where staging lives now, rather than testing on the live blog later.

> **Correction to an earlier version of this plan.** I said a push to main
> would delete the blog. That was overstated. The action tracks what it has
> deployed in a sync state file and only removes files it put there, so a `/blog`
> folder it never uploaded is not in scope for deletion under normal operation.
> The real hazards are narrower: `dangerous-clean-slate` wipes the whole server
> directory including excluded paths, a lost state file changes sync behaviour,
> and human error covers the rest. The exclusion is cheap insurance and states
> the intent explicitly, which is why it stays. It is just not the emergency I
> made it out to be.

**Done when:** a push to main deploys the site and leaves a test file under
`/blog` untouched.

## Phase 01: Foundation

**Goal.** Everything after this depends on knowing who someone is and what they
are allowed to do. Get it right once.

- Laravel 12 installed and serving at `/blog`.
- Breeze for registration, email verification and password reset.
- spatie/laravel-permission with four roles: admin, editor, author, subscriber.
- Socialite for Google, GitHub and LinkedIn, mapped to the subscriber role.
- Two-factor authentication required on admin and editor accounts.
- Filament installed, then themed to Archivo Narrow, IBM Plex Mono and the site palette.

**Done when:** you can sign in with Google, an author can reach the panel but
not another author's drafts, and the panel looks like the rest of the site.

## Phase 02: Authoring

**Goal.** The part you will use every week. If writing a post is unpleasant,
nothing else on this list matters.

- Post model: title, slug, excerpt, body, cover image, status, published_at, author.
- Tags, with the buyer and engineer split built in from the start.
- TipTap mounted inside Filament, with a toolbar we style ourselves.
- Embed nodes for YouTube and Vimeo, code blocks with highlighting, pull quotes.
- Drafts, scheduled publishing, and revision history.
- Per-post SEO fields: meta title, description, social card image, canonical URL.
- KaTeX for maths. Faster and lighter than MathJax, and the coverage is ample for ML notation.
- A notebook import path: `jupyter nbconvert --to html --template basic` produces a fragment that drops into a post.
- Syntax highlighting server side, so it does not cost the reader a JavaScript payload.
- Shareable preview links for drafts, so a post can be reviewed before it is public.

> **This is the phase your actual content depends on.** Kaggle write-ups,
> research notes and ML posts are unreadable without maths, notebooks and code
> blocks. A blog that cannot render them is a blog you will not use.

**Done when:** a real notebook with equations and charts renders correctly end
to end, and a draft can be shared with someone who is not logged in.

## Phase 03: Media

**Goal.** Uploads are where the security mistakes live. Treat every file as
hostile.

- Validate by MIME type and real content, never by file extension.
- Re-encode every image on upload, which strips anything embedded in it.
- Generate responsive sizes with Intervention Image and serve WebP.
- Store outside the web root and serve through a controller.
- A media library screen in Filament with alt text as a required field.

> **Do not host video.** Shared hosting upload limits and bandwidth make it
> painful and expensive. Embed YouTube or Vimeo instead.

**Done when:** a renamed executable uploaded as `.jpg` is rejected, and a real
photo comes back resized and stripped.

## Phase 04: Public

**Goal.** The first phase with a visible payoff, and the one that has to look
like it belongs to the rest of the site.

- Port the Tailwind config and design tokens from the main repository.
- Index as the specimen table used on `/services`, with tag filtering.
- Post pages: the display type, hairline rules, and generous measure.
- RSS feed, sitemap, and JSON-LD Article schema on every post.
- Open Graph and social card images generated per post.
- On-site search over titles, excerpts and body using MySQL full text indexes.
- Pagination on the index and on tag archives.
- Related posts by shared tags. Internal linking is what carries a new domain.
- Author archive pages, ready for the partners being added.
- Reading time and a table of contents on long posts.
- Permanent redirects when a slug changes, so nothing published ever 404s.
- Add the Blog link to the main site's nav. The only change the static site gets.

**Done when:** a post is live, validates in a rich results test, and a stranger
could not tell which pages are static and which are Laravel.

## Phase 05: Performance

**Goal.** The static site is fast. The blog must not be the slow, embarrassing
part of the same domain.

- Full page response caching. This is the single biggest lever: it takes time to first byte from roughly 400ms to under 20ms.
- Confirm OPcache is on with timestamp validation disabled, which shared hosts often leave enabled by default.
- Put a CDN in front for static assets and edge caching.
- Cache invalidation on publish, edit and new comment.
- A performance budget checked before launch, not after.

> **Why this is its own phase.** Typical shared hosting has a time to first byte
> of 900 to 1400ms. Google considers 800ms the threshold for good, and with a
> figure in that range a good Largest Contentful Paint is extremely difficult to
> reach no matter how well the front end is built. Roughly half of Core Web
> Vitals failures trace back to hosting rather than code.

**Done when:** an uncached page beats 800ms and a cached one beats 200ms,
measured from outside the network.

## Phase 06: Audience

**Goal.** Turn readers into a list you own, rather than an audience you rent
from a search engine.

- **Before anything else: SPF, DKIM and DMARC on the sending domain, with alignment.** This is a prerequisite, not a polish item.
- Subscribers table with double opt-in and a verified timestamp.
- One-click unsubscribe using the List-Unsubscribe and List-Unsubscribe-Post headers, honoured within two days.
- Monitor the spam rate in Google Postmaster Tools. Stay under 0.10 percent and never reach 0.30.
- Send through Resend's API from queued jobs.
- A broadcast composer in Filament, reusing the TipTap editor.
- Signup forms on post pages and the blog index.

> **The shared hosting adaptation:** there is no persistent worker process. Use
> the database queue driver with a cron running `queue:work --stop-when-empty`
> every minute. This is the standard pattern and it is fine at blog volume.

> **The gap this closes.** Enforcement of the Gmail, Yahoo and Microsoft bulk
> sender rules has been fully active since May 2026. Mail that fails
> authentication is rejected at the SMTP level and bounced outright, not
> filtered into spam. Compliant senders see about 89 percent inbox placement;
> non-compliant senders lose between 22 and 34 percent to spam. Setting up DNS
> records is an afternoon. Recovering a burned sending domain is not.

**Done when:** a signup produces a confirmation email, a test broadcast reaches
an inbox rather than a spam folder, and a mail tester scores the domain clean on
all three records.

## Phase 07: Community

**Goal.** Storing comments is easy. Moderating them is the actual work, so
build the moderation first.

- Comment model tied to a signed-in user. No anonymous posting.
- Single-level replies. Deep threading is rarely worth what it costs.
- Moderation queue in Filament: approve, reject, block a user, block an address.
- Rate limiting per user and per address, plus a honeypot field.
- Email notification to you on a new comment, and to the author on a reply.
- Comments closed automatically on posts older than a set age.
- A privacy policy for the business side of the site. One exists for MechaBlast; nothing covers blog accounts, comments or the subscriber list.
- A short moderation policy and terms for user submitted content, linked from the comment form.
- Account deletion that actually deletes, since you will now be holding personal data.

**Done when:** you can moderate a day of comments in under a minute from one
screen, and a reader can find out what you store about them and ask for it to go.

## Phase 08: Distribution

**Goal.** Last, because it is the most brittle piece and the one whose APIs
change under you without notice.

- One unified posting API rather than a separate integration per platform.
- Fire from a queued job on publish, so a failure never blocks the post going live.
- Per-post override text, because a good post title is rarely a good social post.
- A log of what was posted where, with a manual retry.

> **Expect X to be awkward.** It now requires your own developer credentials,
> charges per post containing a link, and ended RSS auto-posting in March 2026.
> Everywhere else is straightforward.

**Done when:** publishing a post puts it on LinkedIn without you opening
LinkedIn.

## Phase 09: Hardening

**Goal.** The unglamorous half that decides whether this survives its first
year.

- Own pageview logging: path, referrer, coarse timestamp. No cookies, no third party.
- A reading dashboard in Filament: what is read, what converts to signups.
- Security headers, CSRF everywhere, rate limits on login and comment endpoints.
- Automated database dumps and upload backups, off-server, restored once to prove they work.
- Dependency update schedule, since this is now a PHP app that needs patching.
- Uptime monitoring on `/blog`, alerting to a human.
- Search Console verified, sitemap submitted, Core Web Vitals watched after launch.
- A staging copy of the app, so changes are tested somewhere other than production.

**Done when:** a backup has been restored successfully at least once, and an
outage would page you before a reader noticed.

---

## Constraints worth remembering

**No persistent workers.** Shared hosting cannot hold a queue worker open.
Everything asynchronous runs through cron. Fine at this volume, but it shapes
how newsletters and social posting are written.

**No Redis.** Cache and sessions go to the database or the filesystem. Not a
problem for a blog, and worth knowing before reaching for a package that
assumes otherwise.

**The deploy is the real risk.** The main site's FTP sync is the single thing
most likely to destroy this. That is why phase 00 exists and why nothing else
starts until it is proven.
