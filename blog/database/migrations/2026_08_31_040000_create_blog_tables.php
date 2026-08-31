<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            // Who a tag is written for. The blog serves two readerships and
            // they should be filterable apart rather than blurred together.
            $table->enum('audience', ['buyers', 'engineers', 'both'])->default('both');
            $table->timestamps();
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('body')->nullable();
            $table->string('cover_path')->nullable();
            $table->string('cover_alt')->nullable();

            $table->enum('status', ['draft', 'scheduled', 'published'])->default('draft');
            $table->timestamp('published_at')->nullable();

            // A stable token so a draft can be shown to someone who is not
            // signed in, without making the post public.
            $table->uuid('preview_token')->unique()->nullable();

            $table->string('meta_title')->nullable();
            $table->string('meta_description', 320)->nullable();
            $table->string('canonical_url')->nullable();

            $table->unsignedInteger('reading_minutes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'published_at']);
        });

        Schema::create('post_tag', function (Blueprint $table) {
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->primary(['post_id', 'tag_id']);
        });

        // Slug changes must not orphan a URL that is already published or
        // linked to from somewhere we do not control.
        Schema::create('redirects', function (Blueprint $table) {
            $table->id();
            $table->string('from')->unique();
            $table->string('to');
            $table->unsignedSmallInteger('status')->default(301);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('redirects');
        Schema::dropIfExists('post_tag');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('tags');
    }
};
