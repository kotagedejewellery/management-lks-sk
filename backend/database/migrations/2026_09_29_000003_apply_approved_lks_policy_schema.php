<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE teams ALTER COLUMN department_id SET NOT NULL');
        DB::statement('ALTER TABLE teams DROP CONSTRAINT teams_department_id_foreign');
        DB::statement('ALTER TABLE teams ADD CONSTRAINT teams_department_id_foreign FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE RESTRICT');

        Schema::table('period_participant_snapshots', function (Blueprint $table): void {
            $table->date('participation_start_date');
        });

        Schema::table('audit_logs', function (Blueprint $table): void {
            $table->text('reason')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table): void {
            $table->dropColumn('reason');
        });

        Schema::table('period_participant_snapshots', function (Blueprint $table): void {
            $table->dropColumn('participation_start_date');
        });

        DB::statement('ALTER TABLE teams DROP CONSTRAINT teams_department_id_foreign');
        DB::statement('ALTER TABLE teams ADD CONSTRAINT teams_department_id_foreign FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL');
        DB::statement('ALTER TABLE teams ALTER COLUMN department_id DROP NOT NULL');
    }
};
