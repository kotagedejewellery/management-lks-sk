<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::table('lks_activities')->where('code', 'PUASA_KAMIS')->exists()
            && DB::table('lks_activities')->where('code', 'PUASA_SENIN_KAMIS')->exists()) {
            throw new \RuntimeException('Ditemukan dua aktivitas puasa: PUASA_KAMIS dan PUASA_SENIN_KAMIS. Tentukan satu aktivitas master sebelum menjalankan migrasi ini.');
        }

        Schema::table('lks_activities', function (Blueprint $table): void {
            $table->integer('default_magang_target_count')->nullable()->after('default_minimum_target_count');
            $table->integer('default_magang_minimum_target_count')->nullable()->after('default_magang_target_count');
        });

        Schema::table('period_activities', function (Blueprint $table): void {
            $table->integer('magang_target_count')->nullable()->after('minimum_target_count');
            $table->integer('magang_minimum_target_count')->nullable()->after('magang_target_count');
        });

        DB::statement('UPDATE lks_activities SET default_magang_target_count = GREATEST(1, ROUND(default_target_count * 0.75)::integer), default_magang_minimum_target_count = GREATEST(1, ROUND(default_minimum_target_count * 0.75)::integer) WHERE default_target_count IS NOT NULL AND default_minimum_target_count IS NOT NULL');
        DB::statement('UPDATE period_activities SET magang_target_count = GREATEST(1, ROUND(target_count * 0.75)::integer), magang_minimum_target_count = GREATEST(1, ROUND(minimum_target_count * 0.75)::integer) WHERE target_count IS NOT NULL AND minimum_target_count IS NOT NULL');
        DB::statement('ALTER TABLE lks_activities ADD CONSTRAINT lks_activities_magang_targets_check CHECK (default_magang_target_count IS NULL OR default_magang_minimum_target_count IS NULL OR (default_magang_target_count > 0 AND default_magang_minimum_target_count > 0 AND default_magang_minimum_target_count <= default_magang_target_count))');
        DB::statement('ALTER TABLE period_activities ADD CONSTRAINT period_activities_magang_targets_check CHECK (magang_target_count IS NULL OR magang_minimum_target_count IS NULL OR (magang_target_count > 0 AND magang_minimum_target_count > 0 AND magang_minimum_target_count <= magang_target_count))');
        DB::statement("UPDATE santri_profiles SET category = 'regular' WHERE category IS NULL");
        DB::statement("UPDATE santri_profiles SET category = 'regular' WHERE category = 'magang' AND level = 'leader'");

        DB::statement("UPDATE lks_activities SET code = 'PUASA_SENIN_KAMIS', name = 'Puasa Senin-Kamis', default_max_per_week = 1, default_allowed_weekdays = '{1,4}', default_applicable_levels = '{leader,staff}', updated_at = NOW() WHERE code = 'PUASA_KAMIS'");
        DB::statement("UPDATE period_activities SET activity_code_snapshot = 'PUASA_SENIN_KAMIS', activity_name_snapshot = 'Puasa Senin-Kamis', max_per_week = 1, allowed_weekdays = '{1,4}', applicable_levels = '{leader,staff}', updated_at = NOW() WHERE activity_code_snapshot = 'PUASA_KAMIS' AND period_id IN (SELECT id FROM lks_periods WHERE status = 'draft')");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE period_activities DROP CONSTRAINT IF EXISTS period_activities_magang_targets_check');
        DB::statement('ALTER TABLE lks_activities DROP CONSTRAINT IF EXISTS lks_activities_magang_targets_check');
        DB::statement("UPDATE lks_activities SET code = 'PUASA_KAMIS', name = 'Puasa Kamis', default_allowed_weekdays = '{4}', default_applicable_levels = '{leader}', updated_at = NOW() WHERE code = 'PUASA_SENIN_KAMIS'");
        DB::statement("UPDATE period_activities SET activity_code_snapshot = 'PUASA_KAMIS', activity_name_snapshot = 'Puasa Kamis', allowed_weekdays = '{4}', applicable_levels = '{leader}', updated_at = NOW() WHERE activity_code_snapshot = 'PUASA_SENIN_KAMIS' AND period_id IN (SELECT id FROM lks_periods WHERE status = 'draft')");

        Schema::table('period_activities', function (Blueprint $table): void {
            $table->dropColumn(['magang_target_count', 'magang_minimum_target_count']);
        });

        Schema::table('lks_activities', function (Blueprint $table): void {
            $table->dropColumn(['default_magang_target_count', 'default_magang_minimum_target_count']);
        });
    }
};
