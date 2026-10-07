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
            $table->decimal('staff_passing_threshold', 5, 2)->default(85);
        });

        DB::statement('ALTER TABLE lks_periods ADD CONSTRAINT lks_periods_staff_threshold_check CHECK (staff_passing_threshold BETWEEN 0 AND 100)');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE lks_periods DROP CONSTRAINT IF EXISTS lks_periods_staff_threshold_check');

        Schema::table('lks_periods', function (Blueprint $table): void {
            $table->dropColumn('staff_passing_threshold');
        });
    }
};
