<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lks_periods', function (Blueprint $table): void {
            $table->decimal('magang_passing_threshold', 5, 2)->default(75);
        });

        DB::statement('ALTER TABLE lks_periods ADD CONSTRAINT lks_periods_magang_threshold_check CHECK (magang_passing_threshold BETWEEN 0 AND 100)');

        // Closed periods used the Staff threshold before Magang had its own threshold.
        DB::table('lks_periods')
            ->where('status', 'closed')
            ->update(['magang_passing_threshold' => DB::raw('staff_passing_threshold')]);
    }

    public function down(): void
    {
        throw new \LogicException('Migration ambang Magang bersifat forward-only dan tidak dapat di-rollback.');
    }
};
