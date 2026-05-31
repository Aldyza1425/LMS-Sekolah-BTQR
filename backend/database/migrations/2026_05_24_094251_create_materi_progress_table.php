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
        Schema::create('materi_progresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('siswas')->onDelete('cascade');
            $table->foreignId('materi_id')->constrained('materis')->onDelete('cascade');
            $table->boolean('is_pre_test_done')->default(false);
            $table->integer('pre_test_score')->nullable();
            $table->boolean('is_content_read')->default(false);
            $table->boolean('is_resume_submitted')->default(false);
            $table->text('resume_text')->nullable();
            $table->integer('resume_score')->nullable();
            $table->boolean('is_tugas_submitted')->default(false);
            $table->string('tugas_file_path')->nullable();
            $table->integer('tugas_score')->nullable();
            $table->boolean('is_post_test_done')->default(false);
            $table->integer('post_test_score')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materi_progresses');
    }
};
