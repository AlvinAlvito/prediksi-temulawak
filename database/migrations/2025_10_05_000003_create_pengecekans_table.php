<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pengecekans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pembeli');

            // Simpan bobot pilihan (0..5). 0 = kosong/tidak diisi (untuk CF nanti).
            $table->unsignedTinyInteger('c1')->default(0);
            $table->unsignedTinyInteger('c2')->default(0);
            $table->unsignedTinyInteger('c3')->default(0);
            $table->unsignedTinyInteger('c4')->default(0);
            $table->unsignedTinyInteger('c5')->default(0);
            $table->unsignedTinyInteger('c6')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('pengecekans');
    }
};
