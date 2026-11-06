<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lks_activities', function (Blueprint $table): void {
            $table->boolean('default_menstruation_exempt')->default(false);
        });

        Schema::table('period_activities', function (Blueprint $table): void {
            $table->boolean('menstruation_exempt')->default(false);
        });

        Schema::create('period_menstruation_dispensations', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('period_participant_id')->constrained('period_participant_snapshots')->restrictOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->foreignUuid('recorded_by_user_id')->constrained('users')->restrictOnDelete();
            $table->timestampTz('created_at')->useCurrent();

            $table->unique(['period_participant_id', 'start_date', 'end_date'], 'period_menstruation_dispensations_unique_range');
            $table->index(['period_participant_id', 'start_date', 'end_date'], 'period_menstruation_dispensations_participant_dates_index');
        });
    }

    public function down(): void
    {
        throw new \LogicException('Dispensasi menstruasi bersifat forward-only untuk menjaga riwayat perhitungan periode.');
    }
};
