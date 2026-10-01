<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lks_periods', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name', 100);
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status', 15)->default('draft')->index();
            $table->decimal('final_passing_threshold', 5, 2)->default(90);
            $table->timestampTz('activated_at')->nullable();
            $table->timestampTz('closed_at')->nullable();
            $table->foreignUuid('created_by')->constrained('users')->restrictOnDelete();
            $table->timestampsTz();
        });

        Schema::create('lks_activities', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('code', 50)->unique();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->smallInteger('sort_order')->default(0);
            $table->timestampsTz();
        });

        Schema::create('period_activities', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('period_id')->constrained('lks_periods')->restrictOnDelete();
            $table->foreignUuid('activity_id')->nullable()->constrained('lks_activities')->nullOnDelete();
            $table->string('activity_code_snapshot', 50);
            $table->string('activity_name_snapshot', 150);
            $table->integer('target_count');
            $table->decimal('weight', 8, 2)->default(1);
            $table->boolean('is_active')->default(true);
            $table->smallInteger('sort_order')->default(0);
            $table->timestampsTz();

            $table->unique(['period_id', 'activity_code_snapshot']);
            $table->index(['period_id', 'is_active']);
        });

        DB::statement('ALTER TABLE period_activities ADD COLUMN allowed_weekdays smallint[] NULL');
        DB::statement("ALTER TABLE lks_periods ADD CONSTRAINT lks_periods_status_check CHECK (status IN ('draft', 'active', 'closed'))");
        DB::statement('ALTER TABLE lks_periods ADD CONSTRAINT lks_periods_date_range_check CHECK (end_date >= start_date)');
        DB::statement('ALTER TABLE lks_periods ADD CONSTRAINT lks_periods_threshold_check CHECK (final_passing_threshold BETWEEN 0 AND 100)');
        DB::statement("CREATE UNIQUE INDEX lks_periods_one_active_status ON lks_periods (status) WHERE status = 'active'");
        DB::statement('ALTER TABLE period_activities ADD CONSTRAINT period_activities_target_count_check CHECK (target_count > 0)');
        DB::statement('ALTER TABLE period_activities ADD CONSTRAINT period_activities_weight_check CHECK (weight > 0)');
        DB::statement('ALTER TABLE period_activities ADD CONSTRAINT period_activities_allowed_weekdays_check CHECK (allowed_weekdays IS NULL OR allowed_weekdays <@ ARRAY[1, 2, 3, 4, 5, 6, 7]::smallint[])');
    }

    public function down(): void
    {
        Schema::dropIfExists('period_activities');
        Schema::dropIfExists('lks_activities');
        Schema::dropIfExists('lks_periods');
    }
};
