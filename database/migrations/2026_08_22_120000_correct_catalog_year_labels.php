<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['extra_fees', 'service_items', 'item_options'] as $table) {
            DB::table($table)->where('name', 'like', '% ano%')->update([
                'name' => DB::raw("REPLACE(name, ' ano', ' año')"),
            ]);
        }
    }

    public function down(): void
    {
        foreach (['extra_fees', 'service_items', 'item_options'] as $table) {
            DB::table($table)->where('name', 'like', '% año%')->update([
                'name' => DB::raw("REPLACE(name, ' año', ' ano')"),
            ]);
        }
    }
};
