<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('prediksi_ratings', function (Blueprint $table) {
            $table->id();
            $table->string('dataset_name', 100)->index();
            $table->string('user_label', 100);   // ex: A1, A2 (nama_pembeli)
            $table->string('item_kode', 5);      // ex: C1..C6 yang diprediksi
            $table->decimal('prediksi', 8, 3);   // nilai prediksi
            // opsional: simpan konteks perhitungan
            $table->text('neighbors_used')->nullable();   // JSON { "Ci": {"Cj": sim, "rating": ruj } ... }
            $table->decimal('denominator', 10, 6)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('prediksi_ratings');
    }
};
