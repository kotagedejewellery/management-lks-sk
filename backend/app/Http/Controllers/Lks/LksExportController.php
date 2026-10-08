<?php

namespace App\Http\Controllers\Lks;

use App\Http\Controllers\Controller;
use App\Models\DocumentSignatory;
use App\Models\LksPeriod;
use App\Models\PeriodParticipantSnapshot;
use App\Services\LksScoreCalculator;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LksExportController extends Controller
{
    public function personal(Request $request, LksPeriod $period, LksScoreCalculator $calculator)
    {
        $this->ensureExportable($period);
        abort_if($request->user()->isAdmin(), 403);

        $participant = $period->participants()
            ->where('user_id', $request->user()->getKey())
            ->firstOrFail();

        return $this->individualPdf($period, $participant, $calculator);
    }

    public function participant(Request $request, LksPeriod $period, PeriodParticipantSnapshot $participant, LksScoreCalculator $calculator)
    {
        abort_unless($request->user()->isAdmin(), 403);
        $this->ensureExportable($period);
        abort_unless($participant->period_id === $period->getKey(), 404);

        return $this->individualPdf($period, $participant, $calculator);
    }

    public function summary(Request $request, LksPeriod $period, LksScoreCalculator $calculator)
    {
        abort_unless($request->user()->isAdmin(), 403);
        $this->ensureExportable($period);

        $scores = $calculator->calculatePeriod($period);
        $activityColumns = $period->periodActivities()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($activity): array => ['id' => $activity->getKey(), 'name' => $activity->activity_name_snapshot])
            ->values();
        $rows = $scores->map(fn (array $score): array => $this->reportRow($score, $activityColumns));
        $ranking = $rows->sortByDesc('final_percentage')->values();

        return Pdf::loadView('pdf.lks-period-summary', [
            'period' => $period,
            'organizationName' => config('lks-export.organization_name'),
            'logoPath' => $this->logoPath(),
            'summary' => [
                'participant_count' => $rows->count(),
                'average_percentage' => round($rows->avg('final_percentage') ?? 0, 2),
                'tuntas_count' => $rows->where('final_status', 'tuntas')->count(),
                'belum_tuntas_count' => $rows->where('final_status', 'belum_tuntas')->count(),
                'departments' => $calculator->departmentSummary($scores)->sortByDesc('average_percentage')->values(),
            ],
            'activityColumns' => $activityColumns,
            'rows' => $rows,
            'highest' => $ranking->take(3),
            'lowest' => $ranking->reverse()->take(3)->values(),
            'signatories' => $this->signatoriesForPeriod($period),
            'signatureDate' => $this->signatureDate($period),
            'generatedAt' => now()->format('d-m-Y H:i'),
        ])->setPaper('a4', 'landscape')->download($this->fileName('rekap-lks', $period->name));
    }

    private function individualPdf(LksPeriod $period, PeriodParticipantSnapshot $participant, LksScoreCalculator $calculator)
    {
        $score = $calculator->calculate($participant);

        return Pdf::loadView('pdf.lks-individual', [
            'period' => $period,
            'organizationName' => config('lks-export.organization_name'),
            'logoPath' => $this->logoPath(),
            'report' => $this->reportRow($score),
            'signatories' => $this->signatoriesForPeriod($period),
            'signatureDate' => $this->signatureDate($period),
            'generatedAt' => now()->format('d-m-Y H:i'),
        ])->setPaper('a4', 'portrait')->download($this->fileName('lks', $period->name.'-'.$score['name']));
    }

    /** @param array<string, mixed> $score
     *  @param Collection<int, array{id: string, name: string}> $activityColumns
     *  @return array<string, mixed>
     */
    private function reportRow(array $score, ?Collection $activityColumns = null): array
    {
        $activities = collect($score['activities']);
        $byActivity = $activities->keyBy('id');
        $columns = $activityColumns ?? $activities->map(fn (array $activity): array => ['id' => $activity['id'], 'name' => $activity['name']]);
        $totalScore = round($activities->sum('percentage'), 2);

        return [
            ...$score,
            'activities' => $columns->map(fn (array $activity): ?array => $byActivity->get($activity['id']))->all(),
            'total_score' => $totalScore,
            'maximum_score' => $activities->count() * 100,
            'grade' => $this->grade((float) $score['final_percentage']),
            'level_label' => $score['level'] ?? null,
        ];
    }

    private function grade(float $percentage): string
    {
        return match (true) {
            $percentage >= 91 => 'A',
            $percentage >= 81 => 'B',
            $percentage >= 71 => 'C',
            $percentage >= 61 => 'D',
            default => 'E',
        };
    }

    /** @return list<array{role: string, title: string, name: string, signature_path: ?string}> */
    private function signatoriesForPeriod(LksPeriod $period): array
    {
        $snapshot = collect($period->signatories_snapshot ?? []);
        $signatories = $snapshot->isNotEmpty()
            ? $snapshot
            : DocumentSignatory::query()
                ->orderByRaw("CASE role WHEN 'general_manager' THEN 1 ELSE 2 END")
                ->get(['role', 'title', 'name', 'signature_path'])
                ->map(fn (DocumentSignatory $signatory): array => $signatory->only(['role', 'title', 'name', 'signature_path']));

        return $signatories
            ->map(fn (array $signatory): array => [
                'role' => $signatory['role'],
                'title' => $signatory['title'],
                'name' => $signatory['name'] ?: 'Belum dikonfigurasi',
                'signature_path' => $this->signaturePath($signatory['signature_path'] ?? null),
            ])->all();
    }

    private function signaturePath(?string $path): ?string
    {
        if (! is_string($path) || $path === '') {
            return null;
        }

        $absolutePath = Storage::disk('local')->path($path);

        return is_file($absolutePath) ? $absolutePath : null;
    }

    private function signatureDate(LksPeriod $period): string
    {
        return config('lks-export.document_location').', '.$period->end_date->copy()->locale('id')->translatedFormat('j F Y');
    }

    private function logoPath(): ?string
    {
        $path = public_path('brand/yayasan-riyadhul-jannah.png');

        return is_file($path) ? $path : null;
    }

    private function ensureExportable(LksPeriod $period): void
    {
        abort_unless(in_array($period->status, ['active', 'closed'], true), 404);
    }

    private function fileName(string $prefix, string $name): string
    {
        return Str::slug("{$prefix}-{$name}").'.pdf';
    }
}
