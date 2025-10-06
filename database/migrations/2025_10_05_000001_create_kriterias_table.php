<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('kriterias', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 3)->unique(); // C1..C6
            $table->string('nama');              // Nama kriteria
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('kriterias');
    }
};
