<?php

namespace App\Http\Controllers\Lks;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\DocumentSignatory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DocumentSignatoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->ensureAdmin($request);
        $this->ensureRequiredSignatories();

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
        ]);

        DB::transaction(function () use ($request, $signatory, $data): void {
            $before = $signatory->only(['role', 'title', 'name']);
            $signatory->update([
                'name' => $data['name'],
                'updated_by' => $request->user()->getKey(),
            ]);
            AuditLog::create([
                'actor_user_id' => $request->user()->getKey(),
                'event' => 'document_signatory.updated',
                'auditable_type' => $signatory->getMorphClass(),
                'auditable_id' => $signatory->getKey(),
                'before_data' => $before,
                'after_data' => $signatory->only(['role', 'title', 'name']),
            ]);
        });

        return response()->json(['data' => $this->payload($signatory->refresh())]);
    }

    /** @return array<string, mixed> */
    private function payload(DocumentSignatory $signatory): array
    {
        return [
            'id' => $signatory->getKey(),
            'role' => $signatory->role,
            'title' => $signatory->title,
            'name' => $signatory->name,
            'verification_configured' => filled($signatory->name),
            'updated_at' => $signatory->updated_at?->toIso8601String(),
        ];
    }

    private function ensureAdmin(Request $request): void
    {
        abort_unless($request->user()?->isAdmin(), 403);
    }

    private function ensureRequiredSignatories(): void
    {
        foreach ([
            'general_manager' => 'General Manager',
            'kabid_pengembangan_spiritual' => 'Kabid. Pengembangan Spiritual',
        ] as $role => $title) {
            DocumentSignatory::query()->firstOrCreate(['role' => $role], ['title' => $title]);
        }
    }
}
