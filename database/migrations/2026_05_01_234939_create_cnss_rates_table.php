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
        Schema::create('cnss_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->enum('type', ['employee', 'employer']);
            $table->string('label'); // CNSS, AMO, CIMR, Formation
            $table->decimal('rate_percentage', 5, 2);
            $table->decimal('plafond', 10, 2)->nullable(); // null = pas de plafond
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cnss_rates');
    }
};
