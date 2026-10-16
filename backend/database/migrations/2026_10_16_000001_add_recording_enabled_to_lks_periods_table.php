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
            $table->boolean('recording_enabled')->default(false);
        });

        // Keep every already-active period behaving exactly as it did before this field existed.
        DB::table('lks_periods')->where('status', 'active')->update(['recording_enabled' => true]);
    }

    public function down(): void
    {
        // Forward-only: rolling back must not erase an administrator's recording state.
    }
};
