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
        Schema::create('modul_progresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('siswas')->onDelete('cascade');
            $table->foreignId('modul_id')->constrained('moduls')->onDelete('cascade');
            $table->foreignId('materi_id')->nullable()->constrained('materis')->onDelete('set null');
            $table->integer('persen')->default(0);
            $table->boolean('selesai')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modul_progresses');
    }
};
