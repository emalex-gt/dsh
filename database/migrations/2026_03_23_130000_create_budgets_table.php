<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('pdf_path');
            $table->string('pdf_hash', 64)->nullable();
            $table->string('status', 50)->default('pendiente');
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->string('accepted_name')->nullable();
            $table->string('accepted_ip', 45)->nullable();
            $table->text('accepted_user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
