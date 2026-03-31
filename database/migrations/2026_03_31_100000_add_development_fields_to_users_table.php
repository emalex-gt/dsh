<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('hosting_link')->nullable()->after('direct_demo_mobile_image');
            $table->string('hosting_username')->nullable()->after('hosting_link');
            $table->string('hosting_password')->nullable()->after('hosting_username');
            $table->date('hosting_expires_at')->nullable()->after('hosting_password');
            $table->string('hosting_price')->nullable()->after('hosting_expires_at');
            $table->date('domain_expires_at')->nullable()->after('hosting_price');
            $table->string('domain_price')->nullable()->after('domain_expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'hosting_link',
                'hosting_username',
                'hosting_password',
                'hosting_expires_at',
                'hosting_price',
                'domain_expires_at',
                'domain_price',
            ]);
        });
    }
};
