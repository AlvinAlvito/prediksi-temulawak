<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('subkriterias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kriteria_id')->constrained('kriterias')->cascadeOnDelete();
            $table->string('label');      // mis: "Sangat Besar (≥80 gram)"
            $table->unsignedTinyInteger('bobot'); // 1..5
            $table->unsignedTinyInteger('urutan')->default(0); // untuk sort opsional
            $table->string('keterangan')->nullable(); // rentang/notes opsional
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('subkriterias');
    }
};
