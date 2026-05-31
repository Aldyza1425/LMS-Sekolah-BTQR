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
        Schema::create('try_out_soals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('try_out_id')->constrained('try_outs')->onDelete('cascade');
            $table->text('pertanyaan');
            $table->enum('tipe', ['pilihan_ganda', 'essay'])->default('pilihan_ganda');
            $table->text('pilihan_a')->nullable();
            $table->text('pilihan_b')->nullable();
            $table->text('pilihan_c')->nullable();
            $table->text('pilihan_d')->nullable();
            $table->string('jawaban', 1); // A/B/C/D
            $table->integer('bobot')->default(1);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('try_out_soals');
    }
};
