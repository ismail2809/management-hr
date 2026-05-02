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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->enum('contract_type', ['CDI', 'CDD', 'Stage', 'ANAPEC']);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('salary_base', 10, 2);
            $table->date('trial_period_end')->nullable();
            $table->unsignedSmallInteger('working_hours_per_week')->default(44);
            $table->enum('status', ['actif', 'termine', 'suspendu'])->default('actif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
