<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('annees_scolaires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ecole_setting_id')->constrained('ecole_settings')->cascadeOnDelete();
            $table->string('name', 9); // e.g. 2025-2026
            $table->boolean('is_active')->default(false);
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['ecole_setting_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('annees_scolaires');
    }
};
