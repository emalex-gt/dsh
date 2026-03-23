<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('demos', function (Blueprint $table) {
            $table->string('preview_image_desktop')->nullable()->after('preview_image');
            $table->string('preview_image_mobile')->nullable()->after('preview_image_desktop');
        });
    }

    public function down(): void
    {
        Schema::table('demos', function (Blueprint $table) {
            $table->dropColumn(['preview_image_desktop', 'preview_image_mobile']);
        });
    }
};
