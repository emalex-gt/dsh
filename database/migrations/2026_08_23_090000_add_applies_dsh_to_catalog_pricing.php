<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_items', function (Blueprint $table) {
            $table->boolean('applies_dsh')->default(true)->after('is_required');
        });

        Schema::table('extra_fees', function (Blueprint $table) {
            $table->boolean('applies_dsh')->default(true)->after('amount');
        });
    }

    public function down(): void
    {
        Schema::table('service_items', function (Blueprint $table) {
            $table->dropColumn('applies_dsh');
        });

        Schema::table('extra_fees', function (Blueprint $table) {
            $table->dropColumn('applies_dsh');
        });
    }
};
