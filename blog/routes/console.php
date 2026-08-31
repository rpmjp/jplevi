<?php

use Illuminate\Support\Facades\Schedule;

/*
 * Shared hosting has no persistent worker, so a single cron entry runs the
 * scheduler once a minute and everything below hangs off that.
 */

// Drains whatever is waiting and exits, rather than holding a process open.
Schedule::command('queue:work --stop-when-empty --max-time=50 --tries=3')
    ->everyMinute()
    ->withoutOverlapping();

Schedule::command('blog:backup')->dailyAt('03:20');

// Anything that failed permanently is worth seeing rather than discovering
// months later when someone asks why they never got the newsletter.
Schedule::command('queue:prune-failed --hours=720')->weekly();

// Scheduled posts become visible on their own, but the page cache would go on
// serving the index without them.
Schedule::call(fn () => \App\Http\Middleware\CacheResponse::flush())->hourly();
