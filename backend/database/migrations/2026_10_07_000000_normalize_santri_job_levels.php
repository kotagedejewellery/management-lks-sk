<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement(<<<'SQL'
            UPDATE santri_profiles AS profiles
            SET level = CASE WHEN EXISTS (
                SELECT 1
                FROM user_roles
                INNER JOIN roles ON roles.id = user_roles.role_id
                WHERE user_roles.user_id = profiles.user_id AND roles.code = 'leader'
            ) THEN 'leader' ELSE 'staff' END
        SQL);
        DB::statement('ALTER TABLE santri_profiles ALTER COLUMN level SET NOT NULL');
        DB::statement("ALTER TABLE santri_profiles ADD CONSTRAINT santri_profiles_level_check CHECK (level IN ('staff', 'leader'))");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE santri_profiles DROP CONSTRAINT IF EXISTS santri_profiles_level_check');
        DB::statement('ALTER TABLE santri_profiles ALTER COLUMN level DROP NOT NULL');
    }
};
