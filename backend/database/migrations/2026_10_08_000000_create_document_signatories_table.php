<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_signatories', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('role', 50)->unique();
            $table->string('title', 150);
            $table->string('name', 150)->nullable();
            $table->string('signature_path')->nullable();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestampsTz();
        });

        Schema::table('lks_periods', function (Blueprint $table): void {
            $table->jsonb('signatories_snapshot')->nullable();
        });

        $now = now();
        DB::table('document_signatories')->insert([
            ['id' => (string) Str::uuid(), 'role' => 'general_manager', 'title' => 'General Manager', 'created_at' => $now, 'updated_at' => $now],
            ['id' => (string) Str::uuid(), 'role' => 'kabid_pengembangan_spiritual', 'title' => 'Kabid. Pengembangan Spiritual', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::table('lks_periods', function (Blueprint $table): void {
            $table->dropColumn('signatories_snapshot');
        });

        Schema::dropIfExists('document_signatories');
    }
};
