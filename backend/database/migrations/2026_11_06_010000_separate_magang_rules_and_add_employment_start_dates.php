<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('santri_profiles', function (Blueprint $table): void {
            $table->date('employment_started_on')->nullable()->after('category');
        });

        Schema::table('lks_activities', function (Blueprint $table): void {
            $table->smallInteger('default_magang_max_per_week')->nullable()->after('default_max_per_week');
            $table->boolean('default_magang_is_active')->default(true)->after('is_active');
            $table->boolean('default_magang_menstruation_exempt')->default(false)->after('default_menstruation_exempt');
        });
        DB::statement('ALTER TABLE lks_activities ADD COLUMN default_magang_allowed_weekdays smallint[] NULL');
        DB::statement('ALTER TABLE lks_activities ADD COLUMN default_magang_applicable_genders varchar(10)[] NULL');
        DB::statement('ALTER TABLE lks_activities ADD COLUMN default_magang_applicable_levels varchar(10)[] NULL');

        Schema::table('period_activities', function (Blueprint $table): void {
            $table->smallInteger('magang_max_per_week')->nullable()->after('max_per_week');
            $table->boolean('magang_is_active')->default(true)->after('is_active');
            $table->boolean('magang_menstruation_exempt')->default(false)->after('menstruation_exempt');
        });
        DB::statement('ALTER TABLE period_activities ADD COLUMN magang_allowed_weekdays smallint[] NULL');
        DB::statement('ALTER TABLE period_activities ADD COLUMN magang_applicable_genders varchar(10)[] NULL');
        DB::statement('ALTER TABLE period_activities ADD COLUMN magang_applicable_levels varchar(10)[] NULL');

        // Salin aturan lama sebagai titik awal agar seluruh snapshot yang telah ada tetap bermakna sama.
        DB::statement('UPDATE lks_activities SET default_magang_max_per_week = default_max_per_week, default_magang_allowed_weekdays = default_allowed_weekdays, default_magang_applicable_genders = default_applicable_genders, default_magang_applicable_levels = default_applicable_levels, default_magang_is_active = is_active, default_magang_menstruation_exempt = default_menstruation_exempt');
        DB::statement('UPDATE period_activities SET magang_max_per_week = max_per_week, magang_allowed_weekdays = allowed_weekdays, magang_applicable_genders = applicable_genders, magang_applicable_levels = applicable_levels, magang_is_active = is_active, magang_menstruation_exempt = menstruation_exempt');

        DB::statement('ALTER TABLE lks_activities ADD CONSTRAINT lks_activities_magang_max_per_week_check CHECK (default_magang_max_per_week IS NULL OR default_magang_max_per_week > 0)');
        DB::statement('ALTER TABLE period_activities ADD CONSTRAINT period_activities_magang_max_per_week_check CHECK (magang_max_per_week IS NULL OR magang_max_per_week > 0)');
    }

    public function down(): void
    {
        throw new LogicException('Migrasi aturan Magang bersifat forward-only untuk melindungi snapshot periode yang telah tersimpan.');
    }
};
