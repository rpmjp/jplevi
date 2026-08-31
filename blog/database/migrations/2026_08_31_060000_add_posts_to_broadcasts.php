<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A broadcast is usually a wrapper around posts rather than writing of
        // its own, so the posts it features are a relation, not pasted copy.
        Schema::create('broadcast_post', function (Blueprint $table) {
            $table->foreignId('broadcast_id')->constrained()->cascadeOnDelete();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('position')->default(0);
            $table->primary(['broadcast_id', 'post_id']);
        });

        Schema::table('broadcasts', function (Blueprint $table) {
            $table->string('preheader')->nullable()->after('subject');
            $table->text('intro')->nullable()->after('preheader');
            $table->longText('body')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broadcast_post');
        Schema::table('broadcasts', fn (Blueprint $t) => $t->dropColumn(['preheader', 'intro']));
    }
};
