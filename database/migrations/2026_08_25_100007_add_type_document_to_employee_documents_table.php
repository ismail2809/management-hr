<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_documents', function (Blueprint $table) {
            $table->enum('type_document', [
                'photo', 'extrait_naissance', 'cin', 'carte_cnss',
                'rib', 'diplome', 'contrat_anapec', 'autre',
            ])->default('autre')->after('employee_id');
        });
    }

    public function down(): void
    {
        Schema::table('employee_documents', function (Blueprint $table) {
            $table->dropColumn('type_document');
        });
    }
};
