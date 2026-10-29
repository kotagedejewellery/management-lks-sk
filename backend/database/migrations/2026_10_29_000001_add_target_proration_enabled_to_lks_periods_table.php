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
            $table->boolean('target_proration_enabled')->default(true);
        });

        // Closed periods predate target proration and must keep their reported result.
        DB::table('lks_periods')
            ->where('status', 'closed')
            ->update(['target_proration_enabled' => false]);
    }

    public function down(): void
    {
        throw new \LogicException('Migration prorata target bersifat forward-only untuk menjaga hasil periode tertutup.');
    }
};
