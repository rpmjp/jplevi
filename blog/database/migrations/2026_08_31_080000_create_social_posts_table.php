<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->string('channel');
            $table->text('message');
            $table->enum('status', ['queued', 'sent', 'failed'])->default('queued');
            $table->text('error')->nullable();
            $table->string('remote_id')->nullable();
            $table->timestamp('posted_at')->nullable();
            $table->timestamps();

            $table->unique(['post_id', 'channel']);
        });

        Schema::table('posts', function (Blueprint $table) {
            // A good headline is rarely a good social post, so this is written
            // separately rather than derived from the title.
            $table->text('social_message')->nullable()->after('canonical_url');
            $table->boolean('share_socially')->default(true)->after('social_message');
        });
    }

    public function down(): void
    {
        Schema::table('posts', fn (Blueprint $t) => $t->dropColumn(['social_message', 'share_socially']));
        Schema::dropIfExists('social_posts');
    }
};
