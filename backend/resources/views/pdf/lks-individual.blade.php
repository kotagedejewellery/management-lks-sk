<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 13mm 14mm 15mm; }
        * { box-sizing: border-box; }
        body { color: #18333a; font-family: DejaVu Sans, sans-serif; font-size: 9.2pt; line-height: 1.45; }
        h1, h2, p { margin: 0; }
        h1, h2 { font-family: DejaVu Serif, serif; }
        .document-header { border-bottom: 1px solid #6e8278; display: table; padding-bottom: 10px; width: 100%; }
        .brand-mark, .document-title { display: table-cell; vertical-align: middle; }
        .brand-mark { width: 48px; }
        .brand-mark img { display: block; height: 40px; max-width: 40px; object-fit: contain; }
        .document-title { text-align: right; }
        .eyebrow, .document-title p { color: #5a706d; font-size: 7.6pt; }
        .eyebrow { font-weight: bold; letter-spacing: .7px; text-transform: uppercase; }
        .document-title h1 { font-size: 18pt; letter-spacing: -.25px; margin: 1px 0 2px; }
        .identity { border-collapse: collapse; margin-top: 14px; width: 100%; }
        .identity td { border-bottom: 1px solid #d9e1da; padding: 5px 0; vertical-align: top; }
        .identity td:nth-child(odd) { color: #5a706d; font-size: 8pt; width: 15%; }
        .identity td:nth-child(even) { font-weight: bold; width: 35%; }
        .summary { border-collapse: separate; border-spacing: 7px 0; margin: 16px -7px 0; width: calc(100% + 14px); }
        .summary td { border: 1px solid #c7d4c8; padding: 8px 7px; text-align: center; width: 25%; }
        .summary span { color: #5a706d; display: block; font-size: 7.2pt; }
        .summary strong { display: block; font-family: DejaVu Serif, serif; font-size: 15pt; line-height: 1.25; margin: 3px 0; }
        .section-title { border-bottom: 1px solid #c7d4c8; font-size: 13pt; margin: 19px 0 0; padding-bottom: 5px; }
        .activity-table { border-collapse: collapse; margin-top: 8px; width: 100%; }
        .activity-table th { background: #eaf2ec; color: #49625b; font-size: 7.5pt; padding: 7px 5px; text-align: left; }
        .activity-table td { border-bottom: 1px solid #d9e1da; padding: 7px 5px; vertical-align: top; }
        .activity-table th:not(:first-child), .activity-table td:not(:first-child) { text-align: center; white-space: nowrap; }
        .empty-activity { color: #5a706d; padding: 14px !important; text-align: center !important; }
        .status-done { color: #16604c; font-weight: bold; }
        .status-open { color: #a3524a; font-weight: bold; }
        .recommendation { border-left: 3px solid #2c765d; color: #334e49; margin-top: 9px; padding: 8px 11px; }
        .signature-location { margin: 24px 0 8px; text-align: right; }
        .signature-grid { display: table; table-layout: fixed; width: 100%; page-break-inside: avoid; }
        .signature-grid > div { display: table-cell; padding-right: 17px; text-align: center; vertical-align: top; }
        .signature-grid > div:last-child { padding-right: 0; }
        .signature-label { color: #5a706d; font-size: 7.8pt; }
        .signature-title { font-weight: bold; margin-top: 2px; }
        .signature-asset, .manual-signature-space { height: 47px; margin: 7px auto 4px; max-width: 115px; }
        .signature-asset { display: block; object-fit: contain; }
        .signature-missing { color: #7c8d86; font-size: 7pt; padding-top: 17px; }
        .manual-signature-space { border-bottom: 1px solid #18333a; width: 88%; }
        .signature-name { font-weight: bold; min-height: 15px; }
        .footer { border-top: 1px solid #d9e1da; color: #5a706d; font-size: 6.9pt; margin-top: 21px; padding-top: 6px; }
    </style>
</head>
<body>
    <header class="document-header">
        <div class="brand-mark">@if($logoPath)<img src="{{ $logoPath }}" alt="Logo organisasi">@endif</div>
        <div class="document-title"><p class="eyebrow">{{ $organizationName }}</p><h1>Lembar Kendali Spiritual</h1><p>Periode {{ $period->name }}</p></div>
    </header>

    <table class="identity">
        <tr><td>Nama</td><td>{{ $report['name'] }}</td><td>Level jabatan</td><td>{{ $report['level'] === 'leader' ? 'Leader' : 'Staff' }}</td></tr>
        <tr><td>Gender</td><td>{{ $report['gender'] === 'akhwat' ? 'Akhwat' : 'Ikhwan' }}</td><td>Leader tim</td><td>{{ $report['leader'] ?: 'Belum ditetapkan' }}</td></tr>
        <tr><td>Departemen</td><td>{{ $report['department'] ?: 'Tanpa departemen' }}</td><td>Tim</td><td>{{ $report['team'] ?: 'Tanpa tim' }}</td></tr>
        <tr><td>Rentang periode</td><td colspan="3">{{ $period->start_date->format('d-m-Y') }} s.d. {{ $period->end_date->format('d-m-Y') }}</td></tr>
    </table>

    <table class="summary"><tr>
        <td><span>Total nilai</span><strong>{{ number_format($report['total_score'], 1, ',', '.') }}</strong><span>dari {{ number_format($report['maximum_score'], 0, ',', '.') }}</span></td>
        <td><span>Nilai akhir</span><strong>{{ number_format($report['final_percentage'], 1, ',', '.') }}%</strong><span>ambang {{ number_format($report['passing_threshold'], 0, ',', '.') }}%</span></td>
        <td><span>Predikat</span><strong>{{ $report['grade'] }}</strong><span>A sampai E</span></td>
        <td><span>Status akhir</span><strong class="{{ $report['final_status'] === 'tuntas' ? 'status-done' : 'status-open' }}">{{ $report['final_status'] === 'tuntas' ? 'Tuntas' : 'Belum tuntas' }}</strong><span>sesuai ambang jabatan</span></td>
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
            <div><p class="signature-label">{{ $loop->first ? 'Disetujui oleh,' : 'Diketahui oleh,' }}</p><p class="signature-title">{{ $signatory['title'] }}</p>@if($signatory['signature_path'])<img class="signature-asset" src="{{ $signatory['signature_path'] }}" alt="TTD digital {{ $signatory['title'] }}">@else<div class="signature-asset signature-missing">TTD digital belum dikonfigurasi</div>@endif<p class="signature-name">{{ $signatory['name'] }}</p></div>
        @endforeach
        <div><p class="signature-label">Diterima oleh,</p><p class="signature-title">Santri Karya</p><div class="manual-signature-space"></div><p class="signature-name">{{ $report['name'] }}</p></div>
    </section>

    <footer class="footer">Dokumen dibuat dari snapshot periode pada {{ $generatedAt }}. Nilai aktivitas dibatasi maksimal 100% dan status akhir mengikuti ambang jabatan peserta.</footer>
</body>
</html>
