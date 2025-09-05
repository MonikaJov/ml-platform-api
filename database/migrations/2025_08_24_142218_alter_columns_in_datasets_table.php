<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('datasets', function (Blueprint $table) {
            $table->string('path')->unique(true)->change();
            $table->boolean('has_null')->default(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('datasets', function (Blueprint $table) {
            $table->string('path')->unique(false)->change();
            $table->boolean('has_null')->change();
        });
    }
};
