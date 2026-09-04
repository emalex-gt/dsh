<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('briefs', function (Blueprint $table) {
            $table->string('confirmation_token_hash', 64)->nullable()->unique()->after('status');
            $table->timestamp('confirmation_expires_at')->nullable()->after('confirmation_token_hash');
            $table->timestamp('confirmed_at')->nullable()->after('confirmation_expires_at');
            $table->string('confirmed_name')->nullable()->after('confirmed_at');
            $table->string('confirmed_ip', 45)->nullable()->after('confirmed_name');
            $table->text('confirmed_user_agent')->nullable()->after('confirmed_ip');
        });
    }

    public function down(): void
    {
        Schema::table('briefs', function (Blueprint $table) {
            $table->dropUnique(['confirmation_token_hash']);
            $table->dropColumn([
                'confirmation_token_hash',
                'confirmation_expires_at',
                'confirmed_at',
                'confirmed_name',
                'confirmed_ip',
                'confirmed_user_agent',
            ]);
        });
    }
};
