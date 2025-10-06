<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('pengecekans', function (Blueprint $table) {
            $table->string('dataset_name', 100)->nullable()->after('id');
            $table->index('dataset_name');
        });
    }
    public function down(): void {
        Schema::table('pengecekans', function (Blueprint $table) {
            $table->dropIndex(['dataset_name']);
            $table->dropColumn('dataset_name');
        });
    }
};
