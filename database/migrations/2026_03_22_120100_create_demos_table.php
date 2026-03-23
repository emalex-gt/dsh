<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('demo_category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('link');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demos');
    }
};
