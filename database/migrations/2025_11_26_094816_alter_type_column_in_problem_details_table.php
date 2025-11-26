<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('problem_details', function (Blueprint $table) {
            $table->enum('type', ['regression', 'classification', 'binary_classification', 'clustering'])->change();
        });
    }

    public function down(): void
    {
        Schema::table('problem_details', function (Blueprint $table) {
            $table->enum('type', ['regression', 'classification', 'clustering'])->change();
        });
    }
};
