<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['admin' => 'Admin', 'leader' => 'Leader', 'santri' => 'Santri Karya'] as $code => $name) {
            if (! DB::table('roles')->where('code', $code)->exists()) {
                DB::table('roles')->insert([
                    'id' => (string) Str::uuid(),
                    'code' => $code,
                    'name' => $name,
                ]);
            }
        }
    }

    public function down(): void
    {
        // Roles may already be assigned to users, so a rollback must not remove them.
    }
};
