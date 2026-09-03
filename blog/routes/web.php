<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\SocialAuthController;
use Illuminate\Support\Facades\Route;

/*
 * The application is already mounted at /blog.
 *
 * public_html/blog is a symlink to this app's public directory, so Apache hands
 * Laravel a base path of /blog and every route here is relative to it. The blog
 * routes used to carry a "blog" prefix on top of that, which put the whole site
 * one level too deep: posts answered on /blog/blog/{slug} and every link the
 * pages emitted pointed there. Nothing else in this file was prefixed, which is
 * why media, sign in and the newsletter always worked.
 *
 * So the blog lives at the root of this file. Order matters at the bottom: the
 * post route is a single loose segment and would otherwise swallow /sign-in and
 * /account, so it is registered last, after every fixed path.
 */

// Media is served from outside the web root. The patterns keep the parameters
// to safe characters, so no request can traverse beyond the media directory.
Route::get('/media/{directory}/{file}', \App\Http\Controllers\MediaController::class)
    // jpg as well as webp: the site is served WebP, but the link preview crop
    // is JPEG because scraper support for WebP is still uneven.
    ->where(['directory' => '[a-z0-9\-]+', 'file' => '[A-Za-z0-9\-]+\.(webp|jpg)'])
    ->name('media');

Route::name('blog.')->middleware(\App\Http\Middleware\CacheResponse::class)->group(function () {
    Route::get('/', [BlogController::class, 'index'])->name('index');

    /*
     * The blog home arrives on two different paths, and this is not a
     * redundancy.
     *
     * Every other URL under the mount reaches Laravel with /blog already
     * stripped: a request for /blog/sign-in arrives as /sign-in. The home page
     * does not. It is the one request the web server answers through
     * DirectoryIndex rather than the rewrite, and it reports enough of a
     * different script name that the base path is left on, so /blog/ arrives as
     * "blog". Both are the index.
     */
    Route::get('/blog', [BlogController::class, 'index']);
    Route::get('/feed.xml', [BlogController::class, 'feed'])->name('feed');
    Route::get('/sitemap.xml', [BlogController::class, 'sitemap'])->name('sitemap');
    Route::get('/by/{user}', [BlogController::class, 'author'])->name('author');
    Route::get('/topic/{category}', [BlogController::class, 'topic'])->name('topic');
});

Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::get('/newsletter/confirm/{token}', [NewsletterController::class, 'confirm'])->name('newsletter.confirm');
// GET for a person clicking the link, POST for the one-click header mail
// clients use. Both must work without the reader signing in to anything.
Route::match(['get', 'post'], '/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe'])
    ->name('newsletter.unsubscribe')->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);

Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('social.callback');
Route::post('/auth/logout', [SocialAuthController::class, 'logout'])->name('social.logout');

Route::post('/{post}/comments', [CommentController::class, 'store'])
    ->middleware('auth')->name('comments.store');

Route::middleware('auth')->group(function () {
    Route::get('/account', [AccountController::class, 'show'])->name('account.show');
    Route::delete('/account', [AccountController::class, 'destroy'])->name('account.destroy');
});

Route::view('/legal/privacy', 'legal.privacy')->name('legal.privacy');
Route::view('/legal/comment-rules', 'legal.moderation')->name('legal.moderation');

Route::view('/sign-in', 'auth.sign-in')->name('sign-in');

/*
 * The old doubled paths.
 *
 * Anything already shared or indexed as /blog/blog/{slug} keeps working and
 * says permanently where the post actually lives now. There is deliberately no
 * redirect for the bare path: that one is the blog's own home page, handled
 * above, and a redirect there sent every reader who clicked Notes to the site
 * root instead.
 */
Route::get('/blog/{path}', fn (string $path) => redirect('/'.$path, 301))
    ->where('path', '.+');

/*
 * Registered last on purpose.
 *
 * {slug} matches any single lowercase segment, so it would answer for /sign-in
 * and /account if it came first. Everything with a fixed path is above it.
 */
Route::get('/{slug}', [BlogController::class, 'show'])
    ->middleware(\App\Http\Middleware\CacheResponse::class)
    ->name('blog.show')
    ->where('slug', '[a-z0-9\-]+');
