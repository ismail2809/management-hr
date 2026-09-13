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
        Schema::create('employee_accidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ecole_setting_id')->constrained('ecole_settings')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->dateTime('date_accident')->nullable();
            $table->boolean('rembourse')->default(false);
            $table->decimal('montant', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->json('dossiers')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_accidents');
    }
};
