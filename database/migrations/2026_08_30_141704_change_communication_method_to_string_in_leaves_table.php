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
        \DB::statement("ALTER TABLE leaves MODIFY COLUMN communication_method VARCHAR(50) NULL");
    }

    public function down(): void
    {
        \DB::statement("ALTER TABLE leaves MODIFY COLUMN communication_method ENUM('telephone','email','whatsapp','courrier','autre') NULL");
    }
};
