<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('service_items')
            ->where(function ($query): void {
                $query
                    ->whereRaw('LOWER(name) like ?', ['%hosting%'])
                    ->orWhereRaw('LOWER(name) like ?', ['%dominio%']);
            })
            ->update(['applies_dsh' => false]);
    }

    public function down(): void
    {
        // Existing catalog entries retain their configured DSH setting on rollback.
    }
};
