<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        @page { size: A4 portrait; margin: 9mm 11mm 10mm; }
        * { box-sizing: border-box; }
        body { color: #18333a; font-family: DejaVu Sans, sans-serif; font-size: 8.3pt; line-height: 1.3; }
        h1, h2, p { margin: 0; }
        h1, h2 { font-family: DejaVu Serif, serif; }
        .document-header { border-bottom: 1px solid #6e8278; display: table; min-height: 86px; padding-bottom: 7px; width: 100%; }
        .brand-mark, .document-title { display: table-cell; vertical-align: middle; }
        .brand-mark { width: 88px; }
        .brand-mark--kgj { text-align: right; width: 104px; }
        .brand-symbol { display: block; height: 82px; margin: 0; width: 82px; }
        .brand-symbol--kgj { height: 64px; margin-left: auto; width: 98px; }
        .brand-symbol img { display: block; height: auto; margin: 0 auto; max-height: 82px; max-width: 82px; width: auto; }
        .brand-symbol--kgj img { max-height: 64px; max-width: 98px; }
        .document-title { padding: 0 10px; text-align: center; }
        .organization-name { color: #5a706d; font-size: 6.6pt; font-weight: bold; letter-spacing: .45px; text-transform: uppercase; white-space: nowrap; }
        .document-title h1 { font-size: 11.6pt; letter-spacing: -.1px; line-height: 1.24; margin: 1px 0; }
        .identity { border-collapse: collapse; margin-top: 16px; width: 100%; }
        .identity td { border-bottom: 1px solid #d9e1da; padding: 3px 0; vertical-align: top; }
        .identity td:nth-child(odd) { color: #5a706d; font-size: 7.3pt; width: 15%; }
        .identity td:nth-child(even) { font-weight: bold; width: 35%; }
        .summary { border-collapse: separate; border-spacing: 6px 0; margin: 18px -6px 0; width: calc(100% + 12px); }
        .summary td { border: 1px solid #c7d4c8; padding: 7px 6px; text-align: center; width: 25%; }
        .summary span { color: #5a706d; display: block; font-size: 6.6pt; }
        .summary strong { display: block; font-family: DejaVu Serif, serif; font-size: 12.5pt; line-height: 1.1; margin: 1px 0; }
        .section-title { border-bottom: 1px solid #c7d4c8; font-size: 11.5pt; margin: 17px 0 0; padding-bottom: 4px; }
        .summary + .section-title { margin-top: 24px; }
        .activity-table { border-collapse: collapse; margin-top: 6px; width: 100%; }
        .activity-table th { background: #eaf2ec; color: #49625b; font-size: 7pt; padding: 5px; text-align: left; }
        .activity-table td { border-bottom: 1px solid #d9e1da; padding: 5px 4px; vertical-align: top; }
        .activity-table th:not(:first-child), .activity-table td:not(:first-child) { text-align: center; white-space: nowrap; }
        .empty-activity { color: #5a706d; padding: 9px !important; text-align: center !important; }
        .status-done { color: #16604c; font-weight: bold; }
        .status-open { color: #a3524a; font-weight: bold; }
        .recommendation { border-left: 1px solid #2c765d; color: #334e49; margin-top: 5px; padding: 5px 7px; }
        .signature-location { margin: 48px 0 5px; text-align: right; }
        .signature-grid { display: table; table-layout: fixed; width: 100%; page-break-inside: avoid; }
        .signature-grid > div { display: table-cell; padding-right: 12px; text-align: center; vertical-align: top; }
        .signature-grid > div:last-child { padding-right: 0; }
        .signature-label { color: #5a706d; font-size: 7pt; }
        .signature-title { font-weight: bold; margin-top: 2px; }
        .manual-signature-space { height: 42px; margin: 4px auto 2px; max-width: 115px; }
        .manual-signature-space { border-bottom: 1px solid #18333a; width: 88%; }
        .signature-name { font-weight: bold; min-height: 15px; }
        .verification-strip { border-top: 1px solid #d9e1da; margin-top: 8px; padding-top: 7px; text-align: center; }
        .verification-qr { display: inline-block; height: 48px; margin-right: 7px; vertical-align: middle; width: 48px; }
        .verification-copy { display: inline-block; text-align: left; vertical-align: middle; }
        .verification-copy strong { color: #16604c; display: block; font-size: 7pt; }
        .verification-copy span { color: #5a706d; display: block; font-size: 6.2pt; }
        .footer { border-top: 1px solid #d9e1da; color: #5a706d; font-size: 6.4pt; margin-top: 10px; padding-top: 4px; }
    </style>
</head>
<body>
    <header class="document-header">
        <div class="brand-mark">@if($logoPath)<span class="brand-symbol"><img src="{{ $logoPath }}" alt="Logo Yayasan Riyadhul Jannah Indonesia"></span>@endif</div>
        <div class="document-title"><h1>Lembar Kendali Spiritual<br>Santri Karya PT. Kotagede Jewellery Group</h1><p class="organization-name">Yayasan Riyadhul Jannah Indonesia</p></div>
        <div class="brand-mark brand-mark--kgj">@if($kotagedeJewelleryLogoPath)<span class="brand-symbol brand-symbol--kgj"><img src="{{ $kotagedeJewelleryLogoPath }}" alt="Logo Kotagede Jewellery"></span>@endif</div>
    </header>

    <table class="identity">
        <tr><td>Nama</td><td>{{ $report['name'] }}</td><td>Level jabatan</td><td>{{ $report['level'] === 'leader' ? 'Leader' : 'Staff' }} · LKS {{ ($report['category'] ?? 'regular') === 'magang' ? 'Magang' : 'Reguler' }}</td></tr>
        <tr><td>Gender</td><td>{{ $report['gender'] === 'akhwat' ? 'Akhwat' : 'Ikhwan' }}</td><td>Leader tim</td><td>{{ $report['leader'] ?: 'Belum ditetapkan' }}</td></tr>
        <tr><td>Departemen</td><td>{{ $report['department'] ?: 'Tanpa departemen' }}</td><td>Tim</td><td>{{ $report['team'] ?: 'Tanpa tim' }}</td></tr>
        <tr><td>Rentang periode</td><td colspan="3">{{ $period->start_date->format('d-m-Y') }} s.d. {{ $period->end_date->format('d-m-Y') }}</td></tr>
    </table>

    <table class="summary"><tr>
        <td><span>Total nilai</span><strong>{{ number_format($report['total_score'], 1, ',', '.') }}</strong><span>dari {{ number_format($report['maximum_score'], 0, ',', '.') }}</span></td>
        <td><span>Nilai akhir</span><strong>{{ number_format($report['final_percentage'], 1, ',', '.') }}%</strong><span>ambang {{ number_format($report['passing_threshold'], 0, ',', '.') }}%</span></td>
        <td><span>Predikat</span><strong>{{ $report['grade'] ?? '—' }}</strong><span>A sampai E</span></td>
        <td><span>Status akhir</span><strong class="{{ $report['final_status'] === 'tuntas' ? 'status-done' : 'status-open' }}">{{ $report['final_status'] === 'tuntas' ? 'Tuntas' : 'Belum tuntas' }}</strong><span>sesuai ambang peserta</span></td>
    </tr></table>

    <h2 class="section-title">Capaian aktivitas</h2>
    <table class="activity-table"><thead><tr><th>Aktivitas</th><th>Target</th><th>Minimal</th><th>Capaian</th><th>Nilai</th><th>Status</th></tr></thead><tbody>
        @forelse($report['activities'] as $activity)
            <tr><td>{{ $activity['name'] }}</td><td>{{ $activity['target_count'] }}</td><td>{{ $activity['minimum_target_count'] }}</td><td>{{ $activity['completed_count'] }}</td><td>{{ number_format($activity['percentage'], 1, ',', '.') }}%</td><td class="{{ $activity['status'] === 'tuntas' ? 'status-done' : 'status-open' }}">{{ $activity['status'] === 'tuntas' ? 'Tuntas' : 'Belum tuntas' }}</td></tr>
        @empty
            <tr><td class="empty-activity" colspan="6">Tidak ada aktivitas yang berlaku untuk peserta pada periode ini.</td></tr>
        @endforelse
    </tbody></table>

    <h2 class="section-title">Rekomendasi</h2>
    <section class="recommendation">{{ $report['recommendation'] }}</section>

    <p class="signature-location">{{ $signatureDate }}</p>
    <section class="signature-grid">
        @foreach($signatories as $signatory)
            <div><p class="signature-label">{{ $loop->first ? 'Disetujui oleh,' : 'Diketahui oleh,' }}</p><p class="signature-title">{{ $signatory['title'] }}</p><p class="signature-name">{{ $signatory['name'] }}</p></div>
        @endforeach
        <div><p class="signature-label">Diterima oleh,</p><p class="signature-title">Santri Karya</p><div class="manual-signature-space"></div><p class="signature-name">{{ $report['name'] }}</p></div>
    </section>

    @if($verification)
        <section class="verification-strip"><img class="verification-qr" src="{{ $verificationQrDataUri }}" alt="QR verifikasi dokumen"><span class="verification-copy"><strong>Dokumen ini Resmi &amp; Sah</strong><span>Scan QR untuk verifikasi · {{ $verification->document_code }}</span></span></section>
    @else
        <section class="verification-strip"><span class="verification-copy"><strong>Dokumen sementara</strong><span>QR verifikasi tersedia setelah periode ditutup.</span></span></section>
    @endif

    <footer class="footer">Dokumen dibuat dari snapshot periode pada {{ $generatedAt }}. Nilai aktivitas dibatasi maksimal 100% dan status akhir mengikuti ambang peserta.</footer>
</body>
</html>
