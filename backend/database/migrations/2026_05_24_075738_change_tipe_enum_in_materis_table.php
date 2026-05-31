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
            $table->enum('tipe', ['dokumen', 'video', 'link', 'post_test', 'teks', 'quiz'])->default('dokumen')->change();
        });
        
        DB::statement("UPDATE materis SET tipe = 'post_test' WHERE tipe = 'quiz'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materis', function (Blueprint $table) {
            //
        });
    }
};
