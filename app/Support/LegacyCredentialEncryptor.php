<?php

namespace App\Support;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class LegacyCredentialEncryptor
{
    /**
     * Converts legacy plaintext credentials without touching valid Laravel ciphertext.
     *
     * @return array{hosting: int, email: int}
     */
    public function encryptPlaintextCredentials(): array
    {
        return [
            'hosting' => $this->encryptColumn('users', 'hosting_password'),
            'email' => $this->encryptColumn('client_email_accounts', 'password'),
        ];
    }

    private function encryptColumn(string $table, string $column): int
    {
        $migrated = 0;

        DB::table($table)
            ->select(['id', $column])
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->orderBy('id')
            ->eachById(function (object $record) use ($table, $column, &$migrated): void {
                $value = (string) $record->{$column};

                if ($this->isLaravelCiphertext($value)) {
                    return;
                }

                DB::table($table)
                    ->where('id', $record->id)
                    ->update([$column => Crypt::encryptString($value)]);

                $migrated++;
            });

        return $migrated;
    }

    private function isLaravelCiphertext(string $value): bool
    {
        try {
            Crypt::decryptString($value);

            return true;
        } catch (DecryptException) {
            return false;
        }
    }
}
