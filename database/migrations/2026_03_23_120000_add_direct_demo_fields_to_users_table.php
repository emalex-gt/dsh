<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('direct_demo_name')->nullable()->after('city');
            $table->string('direct_demo_link')->nullable()->after('direct_demo_name');
            $table->string('direct_demo_desktop_image')->nullable()->after('direct_demo_link');
            $table->string('direct_demo_mobile_image')->nullable()->after('direct_demo_desktop_image');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'direct_demo_name',
                'direct_demo_link',
                'direct_demo_desktop_image',
                'direct_demo_mobile_image',
            ]);
        });
    }
};
