<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            // Machine learning can contain Forecasting. One level is enough:
            // deeper trees are a filing system nobody maintains.
            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->text('intro')->nullable();
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('category_post', function (Blueprint $table) {
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->primary(['category_id', 'post_id']);
        });

        Schema::table('posts', function (Blueprint $table) {
            // Audience comes off the tag and onto the post, where it belongs.
            // A tag was carrying two unrelated ideas at once.
            $table->enum('audience', ['buyers', 'engineers', 'both'])->default('both')->after('status');
        });

        Schema::create('revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('excerpt')->nullable();
            $table->longText('body')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['post_id', 'created_at']);
        });

        // Carry the existing tags across, keeping their audience marking.
        foreach (Tag::all() as $tag) {
            $category = Category::create([
                'name' => $tag->name,
                'slug' => $tag->slug,
            ]);

            foreach ($tag->posts as $post) {
                $post->categories()->syncWithoutDetaching([$category->id]);

                if ($tag->audience !== 'both') {
                    $post->forceFill(['audience' => $tag->audience])->saveQuietly();
                }
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('revisions');
        Schema::table('posts', fn (Blueprint $t) => $t->dropColumn('audience'));
        Schema::dropIfExists('category_post');
        Schema::dropIfExists('categories');
    }
};
