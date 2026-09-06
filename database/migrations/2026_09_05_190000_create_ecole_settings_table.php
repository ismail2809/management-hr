<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecole_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->string('nom_ecole')->default('Mon École');
            $table->string('academie')->nullable();
            $table->string('direction_provinciale')->nullable();
            $table->string('directeur')->nullable();
            $table->string('type_etablissement')->nullable();
            $table->string('code_massare')->nullable();
            $table->text('adresse')->nullable();
            $table->string('ville')->nullable();
            $table->string('code_postal')->nullable();
            $table->string('pays')->default('Maroc');
            $table->string('telephone')->nullable();
            $table->string('fax')->nullable();
            $table->string('email')->nullable();
            $table->string('site_web')->nullable();
            $table->string('annee_scolaire')->nullable();
            $table->string('logo')->nullable();
            $table->string('cachet')->nullable();
            $table->boolean('afficher_logo_pdf')->default(true);
            $table->boolean('afficher_cachet_pdf')->default(true);
            $table->text('entete_document')->nullable();
            $table->text('pied_document')->nullable();
            $table->timestamps();

            $table->unique('company_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecole_settings');
    }
};
