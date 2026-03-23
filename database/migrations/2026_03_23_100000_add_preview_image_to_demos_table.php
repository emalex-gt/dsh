<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('demos', function (Blueprint $table) {
            $table->string('preview_image')->nullable()->after('link');
        });
    }

    public function down(): void
    {
        Schema::table('demos', function (Blueprint $table) {
            $table->dropColumn('preview_image');
        });
    }
};
