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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('matricule')->nullable();
            $table->string('cin')->nullable();
            $table->string('cnss_number')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->date('birth_date')->nullable();
            $table->date('hire_date')->nullable();
            $table->enum('contract_type', ['CDI', 'CDD', 'Stage', 'ANAPEC'])->default('CDI');
            $table->enum('marital_status', ['celibataire', 'marie', 'divorce', 'veuf'])->default('celibataire');
            $table->unsignedTinyInteger('number_of_children')->default(0);
            $table->string('rib')->nullable();
            $table->enum('status', ['actif', 'inactif', 'suspendu'])->default('actif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
