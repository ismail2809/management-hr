<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->enum('severity_level', ['faible', 'moyen', 'eleve'])->nullable()->after('status');
            $table->enum('communication_method', ['telephone', 'email', 'whatsapp', 'courrier', 'autre'])->nullable()->after('severity_level');
            $table->dateTime('appointment_date')->nullable()->after('communication_method');
            $table->text('actions_taken')->nullable()->after('appointment_date');
            $table->text('rh_notes')->nullable()->after('actions_taken');
        });
    }

    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->dropColumn(['severity_level', 'communication_method', 'appointment_date', 'actions_taken', 'rh_notes']);
        });
    }
};
