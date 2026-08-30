<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->enum('marital_status', ['celibataire', 'marie', 'divorce', 'veuf'])->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->enum('marital_status', ['celibataire', 'marie', 'divorce', 'veuf'])->default('celibataire')->change();
        });
    }
};
