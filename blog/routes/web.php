<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Media is served from outside the web root. The patterns keep the parameters
// to safe characters, so no request can traverse beyond the media directory.
Route::get('/media/{directory}/{file}', \App\Http\Controllers\MediaController::class)
    ->where(['directory' => '[a-z0-9\-]+', 'file' => '[A-Za-z0-9\-]+\.webp'])
    ->name('media');

use App\Http\Controllers\BlogController;

Route::prefix('blog')->name('blog.')->middleware(\App\Http\Middleware\CacheResponse::class)->group(function () {
    Route::get('/', [BlogController::class, 'index'])->name('index');
    Route::get('/feed.xml', [BlogController::class, 'feed'])->name('feed');
    Route::get('/sitemap.xml', [BlogController::class, 'sitemap'])->name('sitemap');
    Route::get('/by/{user}', [BlogController::class, 'author'])->name('author');
    Route::get('/{slug}', [BlogController::class, 'show'])->name('show')
        ->where('slug', '[a-z0-9\-]+');
});

use App\Http\Controllers\NewsletterController;

Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::get('/newsletter/confirm/{token}', [NewsletterController::class, 'confirm'])->name('newsletter.confirm');
// GET for a person clicking the link, POST for the one-click header mail
// clients use. Both must work without the reader signing in to anything.
Route::match(['get', 'post'], '/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe'])
    ->name('newsletter.unsubscribe')->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);

use App\Http\Controllers\CommentController;
use App\Http\Controllers\SocialAuthController;

Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])->name('social.redirect');
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('social.callback');
Route::post('/auth/logout', [SocialAuthController::class, 'logout'])->name('social.logout');

Route::post('/blog/{post}/comments', [CommentController::class, 'store'])
    ->middleware('auth')->name('comments.store');

use App\Http\Controllers\AccountController;

Route::middleware('auth')->group(function () {
    Route::get('/account', [AccountController::class, 'show'])->name('account.show');
    Route::delete('/account', [AccountController::class, 'destroy'])->name('account.destroy');
});
