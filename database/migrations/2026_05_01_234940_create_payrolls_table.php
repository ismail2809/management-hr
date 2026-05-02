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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->unsignedTinyInteger('month'); // 1–12
            $table->unsignedSmallInteger('year');
            $table->decimal('salaire_brut', 10, 2);
            $table->decimal('total_cnss_employee', 10, 2)->default(0);
            $table->decimal('total_cnss_employer', 10, 2)->default(0);
            $table->decimal('amo_employee', 10, 2)->default(0);
            $table->decimal('amo_employer', 10, 2)->default(0);
            $table->decimal('ir', 10, 2)->default(0);
            $table->decimal('salaire_net', 10, 2)->default(0);
            $table->enum('status', ['brouillon', 'validé', 'payé'])->default('brouillon');
            $table->unique(['company_id', 'employee_id', 'month', 'year']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
