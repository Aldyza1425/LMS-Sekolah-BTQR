<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('try_out_soals', function (Blueprint $table) {
            $table->string('gambar')->nullable()->after('pertanyaan');
        });
    }

    public function down(): void
    {
        Schema::table('try_out_soals', function (Blueprint $table) {
            $table->dropColumn('gambar');
        });
    }
};
