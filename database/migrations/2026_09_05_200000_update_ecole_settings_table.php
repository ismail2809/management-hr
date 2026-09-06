<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ecole_settings', function (Blueprint $table) {
            $table->dropColumn(['academie', 'direction_provinciale', 'directeur', 'type_etablissement', 'annee_scolaire']);
            $table->string('cnss')->nullable()->after('code_massare');
            $table->string('patente')->nullable()->after('cnss');
            $table->string('rc')->nullable()->after('patente');
            $table->string('if_number')->nullable()->after('rc');
        });
    }

    public function down(): void
    {
        Schema::table('ecole_settings', function (Blueprint $table) {
            $table->dropColumn(['cnss', 'patente', 'rc', 'if_number']);
            $table->string('academie')->nullable();
            $table->string('direction_provinciale')->nullable();
            $table->string('directeur')->nullable();
            $table->string('type_etablissement')->nullable();
            $table->string('annee_scolaire')->nullable();
        });
    }
};
