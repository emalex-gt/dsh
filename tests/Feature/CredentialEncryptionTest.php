<?php

namespace Tests\Feature;

use App\Models\ClientEmailAccount;
use App\Models\User;
use App\Support\LegacyCredentialEncryptor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CredentialEncryptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_hosting_password_is_encrypted_at_rest_and_recovered_by_eloquent(): void
    {
        $plainText = 'example-secret-password';
        $user = User::factory()->create(['hosting_password' => $plainText]);

        $storedValue = DB::table('users')->where('id', $user->id)->value('hosting_password');

        $this->assertNotSame($plainText, $storedValue);
        $this->assertStringNotContainsString($plainText, (string) $storedValue);
        $this->assertSame($plainText, $user->fresh()->hosting_password);
    }

    public function test_email_password_is_encrypted_at_rest_and_recovered_by_eloquent(): void
    {
        $plainText = 'example-mail-password';
        $account = ClientEmailAccount::create([
            'user_id' => User::factory()->create()->id,
            'email' => 'mail@example.test',
            'password' => $plainText,
        ]);

        $storedValue = DB::table('client_email_accounts')->where('id', $account->id)->value('password');

        $this->assertNotSame($plainText, $storedValue);
        $this->assertStringNotContainsString($plainText, (string) $storedValue);
        $this->assertSame($plainText, $account->fresh()->password);
    }

    public function test_operational_credentials_are_hidden_from_model_serialization(): void
    {
        $user = User::factory()->create(['hosting_password' => 'example-secret-password']);
        $account = ClientEmailAccount::create([
            'user_id' => $user->id,
            'email' => 'mail@example.test',
            'password' => 'example-mail-password',
        ]);

        $this->assertArrayNotHasKey('hosting_password', $user->toArray());
        $this->assertArrayNotHasKey('password', $account->toArray());
    }

    public function test_authenticated_client_can_view_its_decrypted_operational_credentials(): void
    {
        $user = User::factory()->create([
            'hosting_username' => 'hosting-user',
            'hosting_password' => 'example-secret-password',
        ]);
        ClientEmailAccount::create([
            'user_id' => $user->id,
            'email' => 'mail@example.test',
            'username' => 'mail-user',
            'password' => 'example-mail-password',
        ]);

        $this->actingAs($user)
            ->get(route('client.development.show', 'hosting'))
            ->assertOk()
            ->assertSee('example-secret-password');

        $this->actingAs($user)
            ->get(route('client.development.show', 'email'))
            ->assertOk()
            ->assertSee('example-mail-password');
    }

    public function test_operational_credentials_survive_unrelated_client_updates(): void
    {
        $user = User::factory()->create(['hosting_password' => 'example-secret-password']);
        $account = ClientEmailAccount::create([
            'user_id' => $user->id,
            'email' => 'mail@example.test',
            'password' => 'example-mail-password',
        ]);

        $user->update(['project_name' => 'Updated project']);
        $account->update(['label' => 'Updated label']);

        $this->assertSame('example-secret-password', $user->fresh()->hosting_password);
        $this->assertSame('example-mail-password', $account->fresh()->password);
    }

    public function test_legacy_plaintext_credentials_are_encrypted_without_reencrypting_valid_ciphertext(): void
    {
        $legacyHostingPassword = 'legacy-hosting-password';
        $legacyEmailPassword = 'legacy-email-password';
        $alreadyEncryptedHostingPassword = Crypt::encryptString('already-encrypted-hosting-password');

        $legacyUserId = DB::table('users')->insertGetId([
            'name' => 'Legacy hosting user',
            'email' => 'legacy-hosting@example.test',
            'password' => Hash::make('login-password'),
            'hosting_password' => $legacyHostingPassword,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $encryptedUserId = DB::table('users')->insertGetId([
            'name' => 'Encrypted hosting user',
            'email' => 'encrypted-hosting@example.test',
            'password' => Hash::make('login-password'),
            'hosting_password' => $alreadyEncryptedHostingPassword,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $legacyAccountId = DB::table('client_email_accounts')->insertGetId([
            'user_id' => $legacyUserId,
            'email' => 'legacy-mail@example.test',
            'password' => $legacyEmailPassword,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        app(LegacyCredentialEncryptor::class)->encryptPlaintextCredentials();

        $storedHostingPassword = DB::table('users')->where('id', $legacyUserId)->value('hosting_password');
        $storedEmailPassword = DB::table('client_email_accounts')->where('id', $legacyAccountId)->value('password');

        $this->assertNotSame($legacyHostingPassword, $storedHostingPassword);
        $this->assertNotSame($legacyEmailPassword, $storedEmailPassword);
        $this->assertSame($alreadyEncryptedHostingPassword, DB::table('users')->where('id', $encryptedUserId)->value('hosting_password'));
        $this->assertSame($legacyHostingPassword, User::findOrFail($legacyUserId)->hosting_password);
        $this->assertSame($legacyEmailPassword, ClientEmailAccount::findOrFail($legacyAccountId)->password);
    }

    public function test_legacy_migration_keeps_null_credentials_as_null(): void
    {
        $user = User::factory()->create(['hosting_password' => null]);
        $account = ClientEmailAccount::create([
            'user_id' => $user->id,
            'email' => 'mail@example.test',
            'password' => null,
        ]);

        app(LegacyCredentialEncryptor::class)->encryptPlaintextCredentials();

        $this->assertNull(DB::table('users')->where('id', $user->id)->value('hosting_password'));
        $this->assertNull(DB::table('client_email_accounts')->where('id', $account->id)->value('password'));
    }
}
