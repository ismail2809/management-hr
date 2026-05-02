<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->decimal('overtime_hours', 6, 2)->default(0)->after('salaire_brut');
            $table->decimal('overtime_amount', 10, 2)->default(0)->after('overtime_hours');
            $table->boolean('is_prorata')->default(false)->after('overtime_amount');
            $table->unsignedTinyInteger('worked_days')->nullable()->after('is_prorata');
            $table->unsignedTinyInteger('total_working_days')->nullable()->after('worked_days');
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn(['overtime_hours', 'overtime_amount', 'is_prorata', 'worked_days', 'total_working_days']);
        });
    }
};
