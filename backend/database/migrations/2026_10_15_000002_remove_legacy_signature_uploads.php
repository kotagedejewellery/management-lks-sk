<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    /**
     * Remove all retired signature-upload artefacts. The cleanup is deliberately
     * irreversible: uploaded images must no longer be retained by the system.
     */
    public function up(): void
    {
        DB::statement("UPDATE audit_logs SET before_data = before_data - 'signature_path' WHERE jsonb_exists(before_data, 'signature_path')");
        DB::statement("UPDATE audit_logs SET after_data = after_data - 'signature_path' WHERE jsonb_exists(after_data, 'signature_path')");

        DB::statement(<<<'SQL'
            UPDATE lks_periods
            SET signatories_snapshot = (
                SELECT COALESCE(jsonb_agg(signatory - 'signature_path'), '[]'::jsonb)
                FROM jsonb_array_elements(signatories_snapshot) AS signatory
            )
            WHERE jsonb_typeof(signatories_snapshot) = 'array'
            SQL);

        Storage::disk('local')->deleteDirectory('lks-signatures');

        Schema::table('document_signatories', function (Blueprint $table): void {
            $table->dropColumn('signature_path');
        });
    }

    public function down(): void
    {
        // Intentionally irreversible: the removed private files and references cannot be restored.
    }
};
