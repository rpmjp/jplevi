<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('media', function (Blueprint $table) {
            // The fields WordPress keeps on an attachment. Title and
            // description are what the library is searched and organised by;
            // alt and caption are what the reader gets.
            $table->string('title')->nullable()->after('path');
            $table->text('description')->nullable()->after('caption');

            // The file's own facts, recorded once rather than read off disk on
            // every listing.
            $table->string('original_name')->nullable()->after('description');
            $table->unsignedInteger('width')->nullable()->after('original_name');
            $table->unsignedInteger('height')->nullable()->after('width');
            $table->unsignedInteger('bytes')->nullable()->after('height');
        });

        // Alt text stops being required at the point of upload.
        //
        // It was not null so that no image could be published undescribed, and
        // that intent still holds. But it made uploading a folder of images
        // impossible: every one would need its description typed before any of
        // them could be saved. WordPress takes the other order, and so does
        // this now: upload, then describe, with the library flagging every
        // image still waiting so none of them is quietly forgotten.
        Schema::table('media', function (Blueprint $table) {
            $table->string('alt')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropColumn(['title', 'description', 'original_name', 'width', 'height', 'bytes']);
        });
    }
};
