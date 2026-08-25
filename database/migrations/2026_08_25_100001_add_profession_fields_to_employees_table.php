<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->foreignId('profession_id')->nullable()->constrained('professions')->nullOnDelete()->after('company_id');
            $table->enum('profession_type', ['permanent', 'stagiaire', 'vacataire'])->nullable()->after('profession_id');
            $table->string('birth_place')->nullable()->after('birth_date');
            $table->string('phone_fixed')->nullable()->after('phone');
            $table->string('diploma')->nullable()->after('hire_date');
            $table->string('promotion')->nullable()->after('diploma');
            $table->date('exit_date')->nullable()->after('promotion');
            $table->enum('exit_reason', ['demission', 'decision', 'sanction', 'autre'])->nullable()->after('exit_date');
            $table->text('exit_comment')->nullable()->after('exit_reason');
        });

        DB::statement("ALTER TABLE employees MODIFY COLUMN status ENUM('actif', 'inactif', 'sorti') NOT NULL DEFAULT 'actif'");
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['profession_id']);
            $table->dropColumn([
                'profession_id', 'profession_type', 'birth_place', 'phone_fixed',
                'diploma', 'promotion', 'exit_date', 'exit_reason', 'exit_comment',
            ]);
        });

        DB::statement("ALTER TABLE employees MODIFY COLUMN status ENUM('actif', 'inactif', 'suspendu') NOT NULL DEFAULT 'actif'");
    }
};
