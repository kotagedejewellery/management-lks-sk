<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lks_document_verifications', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('period_id')->constrained('lks_periods')->cascadeOnDelete();
            $table->foreignUuid('participant_id')->constrained('period_participant_snapshots')->cascadeOnDelete();
            $table->string('public_token', 40)->unique();
            $table->string('document_code', 20)->unique();
            $table->timestampTz('issued_at');
            $table->timestampsTz();

            $table->unique(['period_id', 'participant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lks_document_verifications');
    }
};
