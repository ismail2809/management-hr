<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->enum('categorie', ['conge', 'absence'])->default('conge')->after('employee_id');
            $table->string('justificatif')->nullable()->after('reason');
            $table->foreignId('remplacant_id')->nullable()->constrained('employees')->nullOnDelete()->after('justificatif');
            $table->enum('type_cours', ['exercice', 'lecon', 'activite'])->nullable()->after('remplacant_id');
            $table->string('nb_pages')->nullable()->after('type_cours');
            $table->string('intitule_lecon')->nullable()->after('nb_pages');
            $table->string('intitule_activite')->nullable()->after('intitule_lecon');
        });

        // Rendre leave_type_id nullable (pour les absences sans type de congé)
        DB::statement('ALTER TABLE leaves MODIFY COLUMN leave_type_id BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropForeign(['remplacant_id']);
            $table->dropColumn([
                'categorie', 'justificatif', 'remplacant_id',
                'type_cours', 'nb_pages', 'intitule_lecon', 'intitule_activite',
            ]);
        });

        DB::statement('ALTER TABLE leaves MODIFY COLUMN leave_type_id BIGINT UNSIGNED NOT NULL');
    }
};
