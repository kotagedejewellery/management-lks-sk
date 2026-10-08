<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('period_participant_snapshots', function (Blueprint $table): void {
            $table->text('recommendation_snapshot')->nullable()->after('participation_start_date');
        });
    }

    public function down(): void
    {
        Schema::table('period_participant_snapshots', function (Blueprint $table): void {
            $table->dropColumn('recommendation_snapshot');
        });
    }
};
