<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('niveaux_scolaires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ecole_setting_id')->constrained('ecole_settings')->cascadeOnDelete();
            $table->string('name');
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('niveaux_scolaires');
    }
};
