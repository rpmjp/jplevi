<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('name')->nullable();

            // Nothing is sent until this is set. An address that never
            // confirmed is an address that never asked.
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();

            // Single use for confirming, permanent for unsubscribing: the
            // unsubscribe link has to keep working in an email from last year.
            $table->string('confirm_token', 64)->nullable()->unique();
            $table->string('unsubscribe_token', 64)->unique();

            $table->string('source')->nullable();
            $table->ipAddress('signup_ip')->nullable();
            $table->timestamps();

            $table->index(['confirmed_at', 'unsubscribed_at']);
        });

        Schema::create('broadcasts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('subject');
            $table->longText('body');
            $table->enum('status', ['draft', 'sending', 'sent'])->default('draft');
            $table->timestamp('sent_at')->nullable();
            $table->unsignedInteger('recipients')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broadcasts');
        Schema::dropIfExists('subscribers');
    }
};
