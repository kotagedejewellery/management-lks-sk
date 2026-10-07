<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table): void {
            $table->foreignUuid('leader_user_id')->nullable()->after('department_id')->constrained('users')->nullOnDelete();
            $table->index('leader_user_id');
        });

        // Hanya pindahkan penetapan lama yang sudah konsisten: satu tim, satu leader.
        DB::statement(<<<'SQL'
            UPDATE teams AS teams
            SET leader_user_id = candidates.leader_user_id
            FROM (
                SELECT team_id, MIN(leader_user_id::text)::uuid AS leader_user_id
                FROM santri_profiles
                WHERE team_id IS NOT NULL AND leader_user_id IS NOT NULL
                GROUP BY team_id
                HAVING COUNT(DISTINCT leader_user_id) = 1
            ) AS candidates
            WHERE teams.id = candidates.team_id
        SQL);

        DB::statement(<<<'SQL'
            UPDATE santri_profiles AS profiles
            SET leader_user_id = CASE
                WHEN profiles.user_id = teams.leader_user_id THEN NULL
                ELSE teams.leader_user_id
            END
            FROM teams
            WHERE profiles.team_id = teams.id AND teams.leader_user_id IS NOT NULL
        SQL);
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('leader_user_id');
        });
    }
};
