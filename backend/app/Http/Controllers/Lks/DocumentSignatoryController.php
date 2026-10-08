<?php

namespace App\Http\Controllers\Lks;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\DocumentSignatory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocumentSignatoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->ensureAdmin($request);

        return response()->json(['data' => DocumentSignatory::query()
            ->orderByRaw("CASE role WHEN 'general_manager' THEN 1 ELSE 2 END")
            ->get()
            ->map(fn (DocumentSignatory $signatory): array => $this->payload($signatory))
            ->values()]);
    }

    public function update(Request $request, DocumentSignatory $signatory): JsonResponse
    {
        $this->ensureAdmin($request);
        abort_unless(in_array($signatory->role, ['general_manager', 'kabid_pengembangan_spiritual'], true), 404);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'signature' => ['nullable', 'file', 'mimes:png', 'max:2048'],
        ]);
        $oldPath = $signatory->signature_path;
        $newPath = $request->hasFile('signature')
            ? $request->file('signature')->store('lks-signatures', 'local')
            : $oldPath;

        DB::transaction(function () use ($request, $signatory, $data, $newPath): void {
            $before = $signatory->only(['role', 'title', 'name', 'signature_path']);
            $signatory->update([
                'name' => $data['name'],
                'signature_path' => $newPath,
                'updated_by' => $request->user()->getKey(),
            ]);
            AuditLog::create([
                'actor_user_id' => $request->user()->getKey(),
                'event' => 'document_signatory.updated',
                'auditable_type' => $signatory->getMorphClass(),
                'auditable_id' => $signatory->getKey(),
                'before_data' => $before,
                'after_data' => $signatory->only(['role', 'title', 'name', 'signature_path']),
            ]);
        });

        if ($newPath !== $oldPath && $oldPath !== null) {
            Storage::disk('local')->delete($oldPath);
        }

        return response()->json(['data' => $this->payload($signatory->refresh())]);
    }

    public function signature(Request $request, DocumentSignatory $signatory): BinaryFileResponse
    {
        $this->ensureAdmin($request);
        abort_unless($signatory->signature_path && Storage::disk('local')->exists($signatory->signature_path), 404);

        return response()->file(Storage::disk('local')->path($signatory->signature_path), [
            'Cache-Control' => 'private, no-store',
        ]);
    }

    /** @return array<string, mixed> */
    private function payload(DocumentSignatory $signatory): array
    {
        return [
            'id' => $signatory->getKey(),
            'role' => $signatory->role,
            'title' => $signatory->title,
            'name' => $signatory->name,
            'signature_configured' => $signatory->signature_path !== null && Storage::disk('local')->exists($signatory->signature_path),
            'signature_url' => route('api.lks.admin.document-signatories.signature', $signatory),
            'updated_at' => $signatory->updated_at?->toIso8601String(),
        ];
    }

    private function ensureAdmin(Request $request): void
    {
        abort_unless($request->user()?->isAdmin(), 403);
    }
}
