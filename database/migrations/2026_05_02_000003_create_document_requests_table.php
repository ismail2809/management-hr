<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->enum('type', [
                'attestation_travail',
                'attestation_salaire',
                'attestation_cnss',
                'ordre_mission',
                'certificat_travail',
            ]);
            $table->enum('format', ['digital', 'papier'])->default('digital');
            $table->text('reason')->nullable();
            $table->enum('status', ['en_attente', 'traité', 'refusé'])->default('en_attente');
            $table->string('generated_file_path')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_requests');
    }
};
