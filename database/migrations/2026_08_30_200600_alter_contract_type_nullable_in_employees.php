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
            $table->enum('contract_type', ['CDI', 'CDD', 'Stage', 'ANAPEC'])->nullable()->default(null)->change();
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->enum('contract_type', ['CDI', 'CDD', 'Stage', 'ANAPEC'])->default('CDI')->change();
        });
    }
};
