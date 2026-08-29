<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->unique()->after('id');
        });

        // Backfill existing employees
        DB::table('employees')->whereNull('uuid')->orderBy('id')->each(function ($employee) {
            DB::table('employees')
                ->where('id', $employee->id)
                ->update(['uuid' => \Illuminate\Support\Str::uuid()->toString()]);
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
