<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('problem_details', function (Blueprint $table) {
            $table->string('task_id')->after('dataset_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('problem_details', function (Blueprint $table) {
            $table->dropColumn('task_id');
        });
    }
};
