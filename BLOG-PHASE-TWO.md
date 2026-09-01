# Blog, phase two: the parts of WordPress worth having

The blog is live at jplevi.com/blog with 57 passing tests. This is the second
pass: the editing workflow WordPress gets right, built on what is already there,
without the parts of WordPress that exist only for WordPress.

## Three decisions the work depends on

| | Options | Recommendation |
|---|---|---|
| **The panel's look** | WordPress grey and blue, or the site's palette with WordPress's layout | **Our palette, their layout.** Layout is the muscle memory: sidebar, list tables, publish box, quick edit. The colours are fifteen years old and only you will ever see them. |
| **Category archives** | Navigation only and out of the index, or real landing pages with an introduction you write | **Indexed, past a post threshold.** WordPress indexes every archive by default, which is exactly how it produces thin pages that compete with the posts. |
| **Categories and tags, or just categories** | Both, as WordPress ships, or topics as categories with audience kept separate | **Categories only.** Most sites never keep the distinction straight. One idea per control. |

**On order:** phases run in dependency order, with one exception. Phase 17, the
editor, depends on nothing and is what you touch every week, so pull it forward
if it is what you want first.

## What exists today

| Capability | State | Detail |
|---|---|---|
| Posts, editor, scheduling | Built | TipTap, drafts, scheduled publishing, SEO fields, preview tokens, KaTeX, notebook import. |
| Roles and permissions | Built | Admin, editor, author, subscriber over fourteen permissions. |
| Comments | Built | Pre-moderated, bulk approve and reject, blocking, one level of replies. |
| Newsletter | Built | Double opt-in, one-click unsubscribe headers, digest composed from posts. |
| Media | Built | Re-encoded on upload, served from outside the web root, alt text required. |
| Tags | Partial | Flat only. No hierarchy, and the audience split rides on the same object. |
| Trash | Partial | Soft deletes exist in the schema. Nothing in the panel can restore one. |
| Users | Missing | No screen at all. Adding a partner means a seeder or a console command. |
| Revisions | Missing | Claimed in the first roadmap and never built. Overwriting a paragraph loses it. |
| Quick edit and bulk edit | Missing | Changing a slug means opening the full editor. |
| Settings | Missing | Posts per page, moderation rules and comment closing all live in code. |

---

## Phase 10: Taxonomy

**Goal.** Tags are flat and carry two ideas at once. Topics become a hierarchy;
audience stays a separate marker.

- Category model with a parent, so Machine learning can contain Forecasting.
- Audience moves off tags onto the post itself: buyers, engineers, or both.
- Migrate the existing tags to categories in the same change, keeping every URL working.
- Category management in the panel: add, rename, reparent, merge, delete with reassignment.
- Archive pages at `/blog/topic/{slug}`, with an optional introduction per category.
- Archives carry `noindex` until they hold enough posts to be worth landing on.

> **The WordPress trap this avoids:** indexing every archive from the first post
> produces pages with one link on them that compete with the post itself. Half of
> the WordPress SEO plugins exist to switch that off.

**Done when:** every existing post has a category, no old URL 404s, and a
one-post archive is not in the index.

## Phase 11: People

**Goal.** The highest value item on this list: right now a partner cannot be
added without a console.

- List of users with role, post count, last sign in, and joined date.
- Invite by email rather than setting someone's password for them.
- Role assignment, with the last admin protected from demoting themselves.
- Deletion that reassigns their posts rather than deleting the writing.
- Profile screen: name, public byline, email, password, two factor.

> **Guard rail:** nothing in this screen may leave the site with zero admins.
> That is a one-way door.

**Done when:** a partner can be invited, given the author role, write a post,
and be removed without losing it.

## Phase 12: Safety net

**Goal.** Two ways to undo. Deleting the wrong post and overwriting the right
paragraph are the two mistakes that actually happen.

- Trash view with Restore and Delete Permanently, and a count in the status links.
- Trashed posts return 404 and drop out of feeds, sitemap and search immediately.
- Automatic purge of trash older than thirty days, on the existing schedule.
- A revision written on every save that changes the body, title or excerpt.
- Revision browser: side by side, restore, and who changed what when.
- Revisions pruned to the last twenty five per post, so the table cannot run away.

**Done when:** a deleted post can be brought back, and a paragraph deleted three
saves ago can be recovered.

## Phase 13: The list screen

**Goal.** WordPress gets this one right, and it is almost entirely about not
opening the editor to make a small change.

- Status links across the top with live counts: All, Published, Scheduled, Draft, Trash.
- Quick edit in the row: title, slug, date, author, categories, status, comments open.
- Bulk edit: author, status, comments, and adding categories to a selection.
- Comment count per row, linking straight into moderation filtered to that post.
- Preview in the editor rather than only as a link in the list.
- Row actions on hover: Edit, Quick Edit, Trash, View.

**Done when:** fixing a slug, changing a date and retagging five posts all happen
without leaving the list.

## Phase 14: Configuration

**Goal.** Things that are decisions rather than code should not require a deploy
to change.

- Reading: posts per page, whether the feed carries full text or excerpts.
- Discussion: comments open by default, auto close after a number of days, notification recipients.
- Writing: default category, default audience.
- Identity: site title, tagline, the from name and address on outgoing mail.
- A New menu in the panel header: post, category, media, broadcast, user.

> **Not in settings:** credentials. Keys stay in the environment, where they are
> not in the database and not one mis-click from being displayed.

**Done when:** changing posts per page or closing comments after ninety days
needs no deploy.

## Phase 15: The staff bar

**Goal.** Your version fixes both objections to WordPress's: readers never see
it, so it costs them nothing.

- Rendered only for signed in staff. A reader's page is byte for byte unchanged.
- Sits below the nav, in the strip that already exists, styled as site chrome rather than as WordPress.
- Edit this post, when standing on one.
- New: post, category, media.
- Comments awaiting review, with a count, linking to the queue.
- Draft badge when viewing something unpublished, so a preview link is never mistaken for the live page.

**Done when:** you can read the blog, spot a typo, and be in the editor in one
click, and a signed out visitor sees none of it.

## Phase 16: Layout

**Goal.** Structure copied deliberately, because that is the part that makes it
familiar without thinking.

- Sidebar grouped the way WordPress groups it: content, then people, then configuration.
- Publish box in the editor: status, visibility, schedule, trash, and the primary action together.
- List tables with the same rhythm: checkbox, primary column, metadata, date.
- Sensible defaults instead of Screen Options, since a per user column preference is a setting nobody revisits.

> **Open:** whether the skin follows the layout. The recommendation is the site's
> palette; the decision changes this phase and nothing before it, so the earlier
> work can start without settling it.

**Done when:** someone who used WordPress yesterday can find everything here
without being told.

## Phase 17: The editor

**Goal.** Writing interactive posts without touching HTML. This phase has no
dependency on the others, so it is the one to pull forward if it matters most.

The current toolbar is bold, italic, link, two heading levels, quote, code,
lists and file attach. Everything below is missing. WordPress groups its blocks
into Text, Media, Design and Embeds; the Widget and Theme blocks are for
building templates rather than writing, so they are not in scope.

**Text**

- Tables, with header row, alignment, and adding or removing rows and columns.
- Collapsible details, which is WordPress's Details block. The one people actually use for long technical asides.
- Pull quotes, distinct from block quotes, for a line worth setting large.
- Footnotes, numbered automatically and linked both ways.
- Callouts: note, warning, and result, since a research write up needs to flag a caveat without shouting.

**Media**

- Images with caption, alt text, alignment, and width, chosen from the media library rather than uploaded again.
- Galleries, for a set of charts from one experiment.
- Media and text side by side, for a figure with its explanation beside it rather than under it.
- File downloads, for a dataset or a notebook.
- Video by embed. Self hosted video stays out, for bandwidth reasons rather than upload limits.

**Design**

- Columns, so two things can sit side by side.
- Buttons, for a call to action inside a post.
- Tabs and accordions, for showing three approaches without three screens of scrolling.
- Separator and spacer, for deliberate breathing room.
- A read more break, so the index can show a lead paragraph rather than a truncated excerpt.

**Embeds**

- YouTube, Vimeo, X, Bluesky, SoundCloud, Spotify, GitHub gists, CodePen.
- Paste a URL and have it become the embed, which is the part that makes embeds feel effortless.
- Everything served through the existing frame policy, which already allows only YouTube's no cookie domain and Vimeo. Any new provider is a deliberate addition to that list, not an accident.

> **Two decisions inside this phase.**
>
> **Storage format.** The body is HTML today. Structured blocks are far more
> robust stored as JSON, which is what the editor works in natively. Converting
> is a migration, and it is much cheaper now than after fifty posts.
>
> **Interactive blocks need JavaScript on the public page.** Tabs and accordions
> cannot be inline handlers, because the content security policy forbids inline
> script and that policy is what protects the comment section. They will be
> driven by the bundled script reading data attributes.

**Done when:** a Kaggle write up can be published with a table, a collapsible
appendix, a two column figure, a YouTube embed and footnotes, without opening a
code view once.

---

## Deliberately not building

**Pages.** Static pages are the Next site. A second place to make them
guarantees the two diverge.

**Themes, plugins, widgets.** Not a theme platform. A plugin loader on a
one-author blog is remote code execution with no upside.

**Screen options and help tabs.** Answers to a problem good defaults solve, and
contextual help written for an audience of one.

**Pingbacks and post formats.** A dead spam vector, and a taxonomy almost nobody
uses coherently.

**Sticky and password protected posts.** Marginal below a few hundred posts, and
preview tokens already cover showing one person one thing.

**Import and export.** You are not migrating from WordPress. Backups are a
command; imports are notebooks.
