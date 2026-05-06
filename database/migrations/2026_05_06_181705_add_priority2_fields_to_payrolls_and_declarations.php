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
        // Heures sup nuit + absences sans solde
        Schema::table('payrolls', function (Blueprint $table) {
            $table->decimal('overtime_hours_night', 6, 2)->default(0)->after('overtime_amount');
            $table->decimal('overtime_amount_night', 10, 2)->default(0)->after('overtime_hours_night');
            $table->unsignedTinyInteger('absence_days')->default(0)->after('overtime_amount_night');
            $table->decimal('absence_deduction', 10, 2)->default(0)->after('absence_days');
        });

        // Totaux automatiques pour les déclarations
        Schema::table('declarations', function (Blueprint $table) {
            $table->unsignedInteger('employee_count')->default(0)->after('status');
            $table->decimal('total_brut', 12, 2)->default(0)->after('employee_count');
            $table->decimal('total_cnss_employee', 12, 2)->default(0)->after('total_brut');
            $table->decimal('total_cnss_employer', 12, 2)->default(0)->after('total_cnss_employee');
            $table->decimal('total_amo_employee', 12, 2)->default(0)->after('total_cnss_employer');
            $table->decimal('total_amo_employer', 12, 2)->default(0)->after('total_amo_employee');
            $table->decimal('total_ir', 12, 2)->default(0)->after('total_amo_employer');
            $table->decimal('total_net', 12, 2)->default(0)->after('total_ir');
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn(['overtime_hours_night', 'overtime_amount_night', 'absence_days', 'absence_deduction']);
        });

        Schema::table('declarations', function (Blueprint $table) {
            $table->dropColumn([
                'employee_count', 'total_brut',
                'total_cnss_employee', 'total_cnss_employer',
                'total_amo_employee', 'total_amo_employer',
                'total_ir', 'total_net',
            ]);
        });
    }
};
