<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('CREATE UNIQUE INDEX users_email_lower_unique ON users (LOWER(email))');
    }

    public function down(): void
    {
        throw new LogicException('Index email case-insensitive bersifat forward-only untuk melindungi identitas akun yang telah tersimpan.');
    }
};
