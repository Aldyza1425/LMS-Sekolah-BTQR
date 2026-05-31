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
        Schema::table('materis', function (Blueprint $table) {
            $table->boolean('has_pretest')->default(false)->after('durasi');
            $table->boolean('has_resume')->default(false)->after('has_pretest');
            $table->text('resume_instruction')->nullable()->after('has_resume');
            $table->boolean('has_tugas')->default(false)->after('resume_instruction');
            $table->text('tugas_instruction')->nullable()->after('has_tugas');
            $table->boolean('has_posttest')->default(false)->after('tugas_instruction');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materis', function (Blueprint $table) {
            $table->dropColumn(['has_pretest', 'has_resume', 'resume_instruction', 'has_tugas', 'tugas_instruction', 'has_posttest']);
        });
    }
};
