<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categorie_autre_demandes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable()->index();
            $table->string('name');
            $table->boolean('active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
        });

        Schema::table('document_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('categorie_autre_demande_id')->nullable()->after('type');
            $table->foreign('categorie_autre_demande_id')->references('id')->on('categorie_autre_demandes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('document_requests', function (Blueprint $table) {
            $table->dropForeign(['categorie_autre_demande_id']);
            $table->dropColumn('categorie_autre_demande_id');
        });

        Schema::dropIfExists('categorie_autre_demandes');
    }
};
