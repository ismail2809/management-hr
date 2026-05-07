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
        Schema::table('payrolls', function (Blueprint $table) {
            // decimal(5,4) only allows up to 9.9999 — rates like 10.77 overflow
            // decimal(5,2) allows up to 999.99
            $table->decimal('cnss_employee_rate', 5, 2)->nullable()->change();
            $table->decimal('cnss_employer_rate', 5, 2)->nullable()->change();
            $table->decimal('amo_employee_rate',  5, 2)->nullable()->change();
            $table->decimal('amo_employer_rate',  5, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->decimal('cnss_employee_rate', 5, 4)->nullable()->change();
            $table->decimal('cnss_employer_rate', 5, 4)->nullable()->change();
            $table->decimal('amo_employee_rate',  5, 4)->nullable()->change();
            $table->decimal('amo_employer_rate',  5, 4)->nullable()->change();
        });
    }
};
