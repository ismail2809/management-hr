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
            // Snapshot des taux appliqués (évite modification rétroactive)
            $table->decimal('cnss_employee_rate', 5, 4)->nullable()->after('salaire_net');
            $table->decimal('cnss_employer_rate', 5, 4)->nullable()->after('cnss_employee_rate');
            $table->decimal('amo_employee_rate', 5, 4)->nullable()->after('cnss_employer_rate');
            $table->decimal('amo_employer_rate', 5, 4)->nullable()->after('amo_employee_rate');
            $table->decimal('ir_rate_applied', 5, 2)->nullable()->after('amo_employer_rate');

            // Prime d'ancienneté automatique
            $table->unsignedTinyInteger('anciennete_years')->default(0)->after('ir_rate_applied');
            $table->decimal('anciennete_rate', 5, 2)->default(0)->after('anciennete_years');
            $table->decimal('anciennete_amount', 10, 2)->default(0)->after('anciennete_rate');
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn([
                'cnss_employee_rate', 'cnss_employer_rate',
                'amo_employee_rate', 'amo_employer_rate',
                'ir_rate_applied',
                'anciennete_years', 'anciennete_rate', 'anciennete_amount',
            ]);
        });
    }
};
