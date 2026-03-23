<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('budgets', function (Blueprint $table) {
            $table->text('client_notes')->nullable()->after('accepted_user_agent');
            $table->timestamp('responded_at')->nullable()->after('client_notes');
        });
    }

    public function down(): void
    {
        Schema::table('budgets', function (Blueprint $table) {
            $table->dropColumn(['client_notes', 'responded_at']);
        });
    }
};
