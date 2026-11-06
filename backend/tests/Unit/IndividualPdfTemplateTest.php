<?php

namespace Tests\Unit;

use App\Models\LksDocumentVerification;
use App\Models\LksPeriod;
use Tests\TestCase;

class IndividualPdfTemplateTest extends TestCase
{
    public function test_closed_document_places_the_same_verification_qr_above_both_official_names(): void
    {
        $period = new LksPeriod([
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth(),
        ]);
        $verification = new LksDocumentVerification(['document_code' => 'LKS-VERIFY123']);

        $html = view('pdf.lks-individual', [
            'period' => $period,
            'logoPath' => null,
            'kotagedeJewelleryLogoPath' => null,
            'report' => [
                'name' => 'Santri Uji',
                'level' => 'staff',
                'category' => 'regular',
                'gender' => 'ikhwan',
                'leader' => null,
                'department' => null,
                'team' => null,
                'total_score' => 0,
                'maximum_score' => 0,
                'final_percentage' => 0,
                'passing_threshold' => 85,
                'grade' => null,
                'final_status' => 'belum_tuntas',
                'activities' => [],
                'recommendation' => 'Belum ada rekomendasi.',
            ],
            'signatories' => [
                ['role' => 'general_manager', 'title' => 'General Manager', 'name' => 'Pejabat Satu'],
                ['role' => 'kabid_pengembangan_spiritual', 'title' => 'Kabid. Pengembangan Spiritual', 'name' => 'Pejabat Dua'],
            ],
            'signatureDate' => 'Yogyakarta, 1 Januari 2026',
            'generatedAt' => '01-01-2026 00:00',
            'verification' => $verification,
            'verificationQrDataUri' => 'data:image/svg+xml;base64,QR',
        ])->render();

        $this->assertSame(2, substr_count($html, 'class="signature-qr"'));
        $this->assertStringContainsString('LKS-VERIFY123', $html);
        $this->assertLessThan(strpos($html, 'Pejabat Satu'), strpos($html, 'class="signature-qr"'));
    }
}
