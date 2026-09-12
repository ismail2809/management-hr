<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_document_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ecole_setting_id')->nullable()->constrained('ecole_settings')->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->boolean('active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // Changer type_document de ENUM à string pour permettre les codes dynamiques
        Schema::table('employee_documents', function (Blueprint $table) {
            $table->string('type_document')->default('autre')->change();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_document_types');
    }
};
