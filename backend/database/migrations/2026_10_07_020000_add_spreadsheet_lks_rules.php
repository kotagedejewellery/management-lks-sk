<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lks_periods', function (Blueprint $table): void {
            $table->decimal('group_achievement_threshold', 5, 2)->default(85)->after('final_passing_threshold');
        });

        Schema::table('lks_activities', function (Blueprint $table): void {
            $table->integer('default_target_count')->nullable()->after('description');
            $table->integer('default_minimum_target_count')->nullable()->after('default_target_count');
            $table->smallInteger('default_max_per_week')->nullable()->after('default_minimum_target_count');
        });
        DB::statement('ALTER TABLE lks_activities ADD COLUMN default_allowed_weekdays smallint[] NULL');
        DB::statement('ALTER TABLE lks_activities ADD COLUMN default_applicable_genders varchar(10)[] NULL');
        DB::statement('ALTER TABLE lks_activities ADD COLUMN default_applicable_levels varchar(10)[] NULL');

        Schema::table('period_activities', function (Blueprint $table): void {
            $table->integer('minimum_target_count')->nullable()->after('target_count');
            $table->smallInteger('max_per_week')->nullable()->after('minimum_target_count');
        });
        DB::statement('ALTER TABLE period_activities ADD COLUMN applicable_genders varchar(10)[] NULL');
        DB::statement('ALTER TABLE period_activities ADD COLUMN applicable_levels varchar(10)[] NULL');
        DB::statement('UPDATE period_activities SET minimum_target_count = target_count WHERE minimum_target_count IS NULL');
        DB::statement('ALTER TABLE period_activities ALTER COLUMN minimum_target_count SET NOT NULL');
        DB::statement('ALTER TABLE period_activities ADD CONSTRAINT period_activities_minimum_target_count_check CHECK (minimum_target_count > 0 AND minimum_target_count <= target_count)');
        DB::statement('ALTER TABLE period_activities ADD CONSTRAINT period_activities_max_per_week_check CHECK (max_per_week IS NULL OR max_per_week > 0)');

        foreach ([
            ['ABSEN_SUBUH', 'Absen Subuh', 20, 14, null, [1, 2, 3, 4, 5], ['ikhwan', 'akhwat'], ['leader', 'staff']],
            ['SHALAWAT_MUNJIYAT', 'Shalawat Munjiyat', 20, 14, null, [1, 2, 3, 4, 5], ['ikhwan', 'akhwat'], ['leader', 'staff']],
            ['SHALAT_TEPAT_WAKTU', 'Shalat Tepat Waktu di Jam Karya', 20, 14, null, [1, 2, 3, 4, 5], ['ikhwan', 'akhwat'], ['leader', 'staff']],
            ['DZIKIR_PAGI_PETANG', 'Dzikir Pagi Petang', 20, 14, null, [1, 2, 3, 4, 5], ['ikhwan', 'akhwat'], ['leader', 'staff']],
            ['TILAWAH_HARIAN', 'Tilawah Harian 2 Halaman', 20, 14, null, [1, 2, 3, 4, 5], ['ikhwan', 'akhwat'], ['leader', 'staff']],
            ['SHALAT_DHUHA', 'Shalat Dhuha', 20, 14, null, [1, 2, 3, 4, 5], ['ikhwan', 'akhwat'], ['leader', 'staff']],
            ['TAHAJJUD', 'Tahajjud', 8, 6, 2, null, ['ikhwan', 'akhwat'], ['leader', 'staff']],
            ['TILAWATI', 'Tilawati', 4, 3, 1, null, ['ikhwan', 'akhwat'], ['leader', 'staff']],
            ['SUBUHAN_MASJID', 'Subuhan di Masjid', 12, 9, 3, null, ['ikhwan'], ['leader', 'staff']],
            ['PUASA_KAMIS', 'Puasa Kamis', 4, 3, 1, [4], ['ikhwan', 'akhwat'], ['leader']],
        ] as $index => [$code, $name, $target, $minimum, $weeklyLimit, $weekdays, $genders, $levels]) {
            $attributes = [
                'name' => $name,
                'description' => 'Aturan standar LKS berdasarkan lembar kendali spiritual.',
                'default_target_count' => $target,
                'default_minimum_target_count' => $minimum,
                'default_max_per_week' => $weeklyLimit,
                'default_allowed_weekdays' => $weekdays === null ? null : '{'.implode(',', $weekdays).'}',
                'default_applicable_genders' => '{'.implode(',', $genders).'}',
                'default_applicable_levels' => '{'.implode(',', $levels).'}',
                'is_active' => true,
                'sort_order' => $index + 1,
                'updated_at' => now(),
            ];

            $existingCodes = $code === 'SHALAWAT_MUNJIYAT' ? [$code, 'MNJYT'] : [$code];
            $query = DB::table('lks_activities')->whereIn('code', $existingCodes);
            if ($query->exists()) {
                $query->update($attributes + ['code' => $code]);
                continue;
            }

            DB::table('lks_activities')->insert($attributes + [
                'id' => (string) Str::uuid(),
                'code' => $code,
                'created_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE period_activities DROP CONSTRAINT IF EXISTS period_activities_max_per_week_check');
        DB::statement('ALTER TABLE period_activities DROP CONSTRAINT IF EXISTS period_activities_minimum_target_count_check');
        Schema::table('period_activities', function (Blueprint $table): void {
            $table->dropColumn(['minimum_target_count', 'max_per_week']);
        });
        DB::statement('ALTER TABLE period_activities DROP COLUMN IF EXISTS applicable_genders');
        DB::statement('ALTER TABLE period_activities DROP COLUMN IF EXISTS applicable_levels');

        Schema::table('lks_activities', function (Blueprint $table): void {
            $table->dropColumn(['default_target_count', 'default_minimum_target_count', 'default_max_per_week']);
        });
        DB::statement('ALTER TABLE lks_activities DROP COLUMN IF EXISTS default_allowed_weekdays');
        DB::statement('ALTER TABLE lks_activities DROP COLUMN IF EXISTS default_applicable_genders');
        DB::statement('ALTER TABLE lks_activities DROP COLUMN IF EXISTS default_applicable_levels');

        Schema::table('lks_periods', function (Blueprint $table): void {
            $table->dropColumn('group_achievement_threshold');
        });
    }
};
