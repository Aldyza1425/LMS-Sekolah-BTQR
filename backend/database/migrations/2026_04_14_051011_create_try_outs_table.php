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
        Schema::create('try_outs', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 200);
            $table->text('deskripsi')->nullable();
            $table->dateTime('mulai_at');
            $table->dateTime('selesai_at');
            $table->integer('durasi_menit');
            $table->decimal('nilai_lulus', 5, 2);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('pengajars');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('try_outs');
    }
};
