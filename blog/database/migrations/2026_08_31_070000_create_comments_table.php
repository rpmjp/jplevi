<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // One level of replies. Deep threads cost more in layout and
            // moderation than they return in conversation.
            $table->foreignId('parent_id')->nullable()->constrained('comments')->cascadeOnDelete();

            $table->text('body');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->ipAddress('author_ip')->nullable();
            $table->timestamps();

            $table->index(['post_id', 'status', 'created_at']);
        });

        Schema::create('blocks', function (Blueprint $table) {
            $table->id();
            $table->string('value')->unique();
            $table->enum('type', ['email', 'ip']);
            $table->string('reason')->nullable();
            $table->timestamps();
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->boolean('comments_open')->default(true)->after('reading_minutes');
        });
    }

    public function down(): void
    {
        Schema::table('posts', fn (Blueprint $t) => $t->dropColumn('comments_open'));
        Schema::dropIfExists('blocks');
        Schema::dropIfExists('comments');
    }
};
