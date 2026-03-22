<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('project_name')->nullable()->after('is_admin');
            $table->string('legal_name')->nullable()->after('project_name');
            $table->string('brand_name')->nullable()->after('legal_name');
            $table->string('contact_name')->nullable()->after('brand_name');
            $table->string('contact_role')->nullable()->after('contact_name');
            $table->string('contact_phone')->nullable()->after('contact_role');
            $table->string('country', 120)->nullable()->after('contact_phone');
            $table->string('city', 120)->nullable()->after('country');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'project_name',
                'legal_name',
                'brand_name',
                'contact_name',
                'contact_role',
                'contact_phone',
                'country',
                'city',
            ]);
        });
    }
};
