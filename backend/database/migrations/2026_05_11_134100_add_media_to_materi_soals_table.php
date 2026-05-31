<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('materi_soals', function (Blueprint $table) {
            $table->string('media_type')->default('None')->after('jawaban');
            $table->string('video_link')->nullable()->after('media_type');
            $table->string('video_path')->nullable()->after('video_link');
            $table->string('pdf_path')->nullable()->after('video_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materi_soals', function (Blueprint $table) {
            $table->dropColumn(['media_type', 'video_link', 'video_path', 'pdf_path']);
        });
    }
};
