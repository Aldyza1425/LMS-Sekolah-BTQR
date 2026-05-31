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
            $table->enum('tipe_soal', ['pre_test', 'post_test'])->default('post_test')->after('materi_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materi_soals', function (Blueprint $table) {
            //
        });
    }
};
