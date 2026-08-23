<?php

use App\Support\LegacyCredentialEncryptor;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('hosting_password')->nullable()->change();
        });

        Schema::table('client_email_accounts', function (Blueprint $table) {
            $table->text('password')->nullable()->change();
        });

        app(LegacyCredentialEncryptor::class)->encryptPlaintextCredentials();
    }

    public function down(): void
    {
        // Keep encrypted TEXT columns to avoid exposing or truncating credentials.
    }
};
