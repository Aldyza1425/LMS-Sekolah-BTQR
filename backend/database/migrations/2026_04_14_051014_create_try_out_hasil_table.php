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
        Schema::create('try_out_hasil', function (Blueprint $table) {
            $table->id();
            $table->foreignId('try_out_id')->constrained('try_outs')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('siswas')->onDelete('cascade');
            $table->decimal('nilai', 5, 2);
            $table->boolean('lulus')->default(false);
            $table->timestamp('mulai_at')->nullable();
            $table->timestamp('selesai_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('try_out_hasil');
    }
};
