<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('item_similarities', function (Blueprint $table) {
            $table->id();
            $table->string('item_i', 3); // C1..C6
            $table->string('item_j', 3); // C1..C6
            $table->decimal('similarity', 8, 3)->default(0);
            $table->timestamp('computed_at')->nullable();
            $table->timestamps();

            $table->unique(['item_i', 'item_j']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('item_similarities');
    }
};
