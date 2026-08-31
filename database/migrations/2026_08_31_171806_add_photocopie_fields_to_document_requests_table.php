<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_requests', function (Blueprint $table) {
            $table->string('photocopie_sous_type')->nullable()->after('description');
            $table->string('photocopie_niveau')->nullable()->after('photocopie_sous_type');
            $table->string('photocopie_groupe')->nullable()->after('photocopie_niveau');
            $table->unsignedSmallInteger('photocopie_nb_copies')->nullable()->after('photocopie_groupe');
            $table->date('photocopie_date_souhaitee')->nullable()->after('photocopie_nb_copies');
        });
    }

    public function down(): void
    {
        Schema::table('document_requests', function (Blueprint $table) {
            $table->dropColumn([
                'photocopie_sous_type',
                'photocopie_niveau',
                'photocopie_groupe',
                'photocopie_nb_copies',
                'photocopie_date_souhaitee',
            ]);
        });
    }
};
