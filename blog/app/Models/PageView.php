<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageView extends Model
{
    public $timestamps = false;

    protected $fillable = ['path', 'post_id', 'referrer_host', 'viewed_on', 'views'];

    /*
     * viewed_on is deliberately left uncast. A date cast writes
     * "2026-08-31 00:00:00" on insert while the lookup searches for
     * "2026-08-31", so the row never matches itself and every view collides
     * with the unique index instead of incrementing.
     */
}
