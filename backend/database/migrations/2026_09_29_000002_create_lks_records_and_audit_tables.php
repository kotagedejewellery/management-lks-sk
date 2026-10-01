<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('period_participant_snapshots', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('period_id')->constrained('lks_periods')->restrictOnDelete();
            $table->foreignUuid('user_id')->constrained()->restrictOnDelete();
            $table->string('participant_name_snapshot', 150);
            $table->string('gender_snapshot', 10);
            $table->uuid('department_id_snapshot')->nullable();
            $table->string('department_name_snapshot', 100)->nullable();
            $table->uuid('team_id_snapshot')->nullable();
            $table->string('team_name_snapshot', 100)->nullable();
            $table->uuid('leader_user_id_snapshot')->nullable();
            $table->string('leader_name_snapshot', 150)->nullable();
            $table->string('category_snapshot', 100)->nullable();
            $table->string('level_snapshot', 100)->nullable();
            $table->timestampTz('created_at')->useCurrent();

            $table->unique(['period_id', 'user_id']);
            $table->index(['period_id', 'gender_snapshot']);
            $table->index(['period_id', 'leader_user_id_snapshot']);
            $table->index(['period_id', 'department_id_snapshot']);
        });

        Schema::create('lks_checklists', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('period_participant_id')->constrained('period_participant_snapshots')->restrictOnDelete();
            $table->foreignUuid('period_activity_id')->constrained('period_activities')->restrictOnDelete();
            $table->date('checklist_date');
            $table->boolean('is_completed')->default(true);
            $table->foreignUuid('recorded_by_user_id')->constrained('users')->restrictOnDelete();
            $table->timestampsTz();

            $table->unique(['period_participant_id', 'period_activity_id', 'checklist_date']);
            $table->index(['period_activity_id', 'checklist_date']);
        });

        Schema::create('audit_logs', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event', 100);
            $table->string('auditable_type', 100);
            $table->uuid('auditable_id');
            $table->jsonb('before_data')->nullable();
            $table->jsonb('after_data')->nullable();
            $table->timestampTz('created_at')->useCurrent();

            $table->index(['auditable_type', 'auditable_id', 'created_at'], 'audit_logs_auditable_created_index');
        });

        DB::statement("ALTER TABLE period_participant_snapshots ADD CONSTRAINT period_participant_snapshots_gender_check CHECK (gender_snapshot IN ('ikhwan', 'akhwat'))");
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('lks_checklists');
        Schema::dropIfExists('period_participant_snapshots');
    }
};
