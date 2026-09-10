<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nature_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ecole_setting_id')->nullable()->index();
            $table->string('name');
            $table->boolean('active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('ecole_setting_id')->references('id')->on('ecole_settings')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nature_documents');
    }
};
