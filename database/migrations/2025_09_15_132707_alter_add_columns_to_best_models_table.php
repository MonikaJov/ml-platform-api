<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('best_models', function (Blueprint $table) {
            $table->foreignId('dataset_id')->after('problem_detail_id')->constrained();
            $table->json('performance')->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('best_models', function (Blueprint $table) {
            $table->dropForeign('best_models_dataset_id_foreign');
            $table->dropColumn('dataset_id');
            $table->dropColumn('performance');
        });
    }
};
