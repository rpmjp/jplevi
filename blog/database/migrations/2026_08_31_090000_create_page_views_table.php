<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
         * Our own reading numbers. No cookies, no third party, no identifier
         * that follows anyone between visits: a path, where they came from, and
         * a day. Enough to know what people read, not enough to profile them,
         * which is also what lets the site keep claiming it does not track.
         */
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->string('path');
            $table->foreignId('post_id')->nullable()->constrained()->nullOnDelete();
            $table->string('referrer_host')->nullable();
            $table->date('viewed_on');
            $table->unsignedInteger('views')->default(1);

            $table->unique(['path', 'referrer_host', 'viewed_on']);
            $table->index(['post_id', 'viewed_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_views');
    }
};
