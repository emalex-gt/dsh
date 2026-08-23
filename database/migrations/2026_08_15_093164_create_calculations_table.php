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
        Schema::create('calculations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catalog_service_id')->constrained()->cascadeOnDelete();
            $table->string('scenario', 64);
            $table->foreignId('library_type_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('days')->default(0);
            $table->unsignedInteger('hours_per_day')->default(0);
            $table->unsignedInteger('people')->default(0);
            $table->decimal('rate_per_hour', 12, 2)->default(0);
            $table->unsignedInteger('subtotal_hours')->default(0);
            $table->decimal('subtotal_amount', 12, 2)->default(0);
            $table->decimal('dsh_percentage', 5, 2)->default(0);
            $table->decimal('dsh_amount', 12, 2)->default(0);
            $table->decimal('base_total', 12, 2)->default(0);
            $table->decimal('total_final', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calculations');
    }
};
