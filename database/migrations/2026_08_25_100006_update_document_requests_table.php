<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_requests', function (Blueprint $table) {
            $table->enum('categorie', ['document', 'autre'])->default('document')->after('employee_id');
            $table->text('description')->nullable()->after('reason');
            $table->string('fichier_final')->nullable()->after('generated_file_path');
            $table->unsignedInteger('nb_telechargements')->default(0)->after('fichier_final');
        });

        DB::statement("ALTER TABLE document_requests MODIFY COLUMN type ENUM(
            'attestation_travail','attestation_salaire','bulletin_paie','attestation_ir',
            'credit_irrevocable','attestation_cnss','ordre_mission','certificat_travail',
            'materiel','grande_salle','photocopie','rencontre_parents',
            'rencontre_direction','formation','activites','divers'
        ) NULL");

        DB::statement("ALTER TABLE document_requests MODIFY COLUMN status ENUM('en_attente','approuvé','refusé') NOT NULL DEFAULT 'en_attente'");
    }

    public function down(): void
    {
        Schema::table('document_requests', function (Blueprint $table) {
            $table->dropColumn(['categorie', 'description', 'fichier_final', 'nb_telechargements']);
        });
    }
};
