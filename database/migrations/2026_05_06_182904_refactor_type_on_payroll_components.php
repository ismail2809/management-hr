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
        // 1. Étendre l'enum pour accepter les nouveaux types en plus des anciens
        DB::statement("ALTER TABLE payroll_components MODIFY COLUMN type ENUM(
            'prime','retenue','avantage',
            'prime_imposable','prime_non_imposable','avantage_imposable'
        ) NOT NULL");

        // 2. Migrer les données existantes
        DB::statement("UPDATE payroll_components SET type = 'prime_imposable'
            WHERE type = 'prime' AND taxable = 1");
        DB::statement("UPDATE payroll_components SET type = 'prime_non_imposable'
            WHERE type = 'prime' AND taxable = 0");
        DB::statement("UPDATE payroll_components SET type = 'avantage_imposable'
            WHERE type = 'avantage'");
        // 'retenue' reste inchangé

        // 3. Réduire l'enum aux nouveaux types seulement
        DB::statement("ALTER TABLE payroll_components MODIFY COLUMN type ENUM(
            'prime_imposable','prime_non_imposable','avantage_imposable','retenue'
        ) NOT NULL");

        // 4. Supprimer la colonne taxable devenue redondante
        Schema::table('payroll_components', function (Blueprint $table) {
            $table->dropColumn('taxable');
        });
    }

    public function down(): void
    {
        // Restaurer taxable + anciens types
        Schema::table('payroll_components', function (Blueprint $table) {
            $table->boolean('taxable')->default(true)->after('amount');
        });

        DB::statement("ALTER TABLE payroll_components MODIFY COLUMN type ENUM(
            'prime_imposable','prime_non_imposable','avantage_imposable','retenue',
            'prime','avantage'
        ) NOT NULL");

        DB::statement("UPDATE payroll_components SET type = 'prime', taxable = 1 WHERE type = 'prime_imposable'");
        DB::statement("UPDATE payroll_components SET type = 'prime', taxable = 0 WHERE type = 'prime_non_imposable'");
        DB::statement("UPDATE payroll_components SET type = 'avantage', taxable = 1 WHERE type = 'avantage_imposable'");

        DB::statement("ALTER TABLE payroll_components MODIFY COLUMN type ENUM(
            'prime','retenue','avantage'
        ) NOT NULL");
    }
};
