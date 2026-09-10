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
            $table->string('nom_ecole')->default('Mon École');
            $table->string('code_massare')->nullable();
            $table->string('cnss')->nullable();
            $table->string('patente')->nullable();
            $table->string('rc')->nullable();
            $table->string('if_number')->nullable();
            $table->text('adresse')->nullable();
            $table->string('ville')->nullable();
            $table->string('code_postal')->nullable();
            $table->string('pays')->default('Maroc');
            $table->string('telephone')->nullable();
            $table->string('fax')->nullable();
            $table->string('email')->nullable();
            $table->string('site_web')->nullable();
            $table->string('logo')->nullable();
            $table->string('cachet')->nullable();
            $table->boolean('afficher_logo_pdf')->default(true);
            $table->boolean('afficher_cachet_pdf')->default(true);
            $table->text('entete_document')->nullable();
            $table->text('pied_document')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecole_settings');
    }
};
