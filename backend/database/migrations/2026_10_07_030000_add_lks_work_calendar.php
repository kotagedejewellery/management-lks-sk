<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calendar_holidays', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->date('holiday_date')->unique();
            $table->string('name', 150);
            $table->string('type', 30)->default('public_holiday');
            $table->timestamps();
        });

        Schema::create('period_holiday_snapshots', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('period_id')->constrained('lks_periods')->cascadeOnDelete();
            $table->date('holiday_date');
            $table->string('name', 150);
            $table->string('type', 30);
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['period_id', 'holiday_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('period_holiday_snapshots');
        Schema::dropIfExists('calendar_holidays');
    }
};
