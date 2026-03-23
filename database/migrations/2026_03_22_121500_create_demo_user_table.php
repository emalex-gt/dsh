<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demo_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('demo_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_recommended')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'demo_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demo_user');
    }
};
