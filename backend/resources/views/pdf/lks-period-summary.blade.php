<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 13mm 11mm; }
        * { box-sizing: border-box; }
        body { color: #17313a; font-family: DejaVu Sans, sans-serif; font-size: 8pt; line-height: 1.35; }
        h1, h2, h3, p { margin: 0; }
        h1, h2, h3 { font-family: DejaVu Serif, serif; }
        .document-header { border-bottom: 1px solid #17313a; display: table; padding-bottom: 10px; width: 100%; }
        .document-header > div { display: table-cell; vertical-align: middle; }
        .brand-mark { width: 42px; }
        .brand-mark img { height: 38px; max-width: 38px; object-fit: contain; }
        .document-title { text-align: right; }
        .document-title h1 { font-size: 17pt; }
        .document-title p, .muted { color: #52646a; font-size: 7.5pt; }
        .summary { border-collapse: collapse; margin-top: 14px; width: 100%; }
        .summary td { border: 1px solid #c5d0c8; padding: 8px; text-align: center; width: 25%; }
        .summary span { color: #52646a; display: block; font-size: 7pt; }
        .summary strong { display: block; font-family: DejaVu Serif, serif; font-size: 15pt; margin: 2px 0; }
        .section-title { border-bottom: 1px solid #c5d0c8; font-size: 12pt; margin: 18px 0 0; padding-bottom: 5px; }
        .department-table, .report-table { border-collapse: collapse; margin-top: 9px; width: 100%; }
        .department-table th, .report-table th { background: #edf3ee; color: #52646a; font-size: 6.5pt; padding: 6px 4px; text-align: left; }
        .department-table td, .report-table td { border-bottom: 1px solid #dce2dc; padding: 6px 4px; vertical-align: top; }
        .report-table { font-size: 6.4pt; }
        .report-table th { font-size: 5.8pt; white-space: nowrap; }
        .report-table .activity-value { text-align: center; white-space: nowrap; }
        .status-done { color: #155244; font-weight: bold; }
        .status-open { color: #9c4e47; font-weight: bold; }
        .rankings { display: table; margin-top: 9px; table-layout: fixed; width: 100%; }
        .rankings > div { display: table-cell; padding-right: 20px; vertical-align: top; }
        .rankings > div:last-child { padding-right: 0; }
        .rankings ol { margin: 7px 0 0; padding-left: 16px; }
        .rankings li { border-bottom: 1px solid #dce2dc; padding: 4px 0; }
        .signature-grid { display: table; margin-top: 28px; table-layout: fixed; width: 100%; }
        .signature-grid > div { display: table-cell; padding-right: 65px; text-align: center; vertical-align: top; }
        .signature-grid > div:last-child { padding-right: 0; }
        .signature-image, .signature-missing { height: 45px; margin: 5px auto; max-width: 130px; }
        .signature-image { display: block; object-fit: contain; }
        .signature-missing { color: #52646a; font-size: 7pt; padding-top: 17px; }
        .signature-name { border-top: 1px solid #17313a; font-weight: bold; padding-top: 4px; }
        .signature-location { margin: 20px 0 7px; text-align: right; }
        .footer { border-top: 1px solid #dce2dc; color: #52646a; font-size: 6.5pt; margin-top: 18px; padding-top: 6px; }
    </style>
</head>
<body>
    <header class="document-header">
        <div class="brand-mark">@if($logoPath)<img src="{{ $logoPath }}" alt="Logo organisasi">@endif</div>
        <div class="document-title"><h1>Rekap LKS Periode</h1><p>{{ $organizationName }} · {{ $period->name }} · {{ $period->start_date->format('d-m-Y') }} s.d. {{ $period->end_date->format('d-m-Y') }}</p></div>
    </header>

    <table class="summary"><tr><td><span>Peserta</span><strong>{{ $summary['participant_count'] }}</strong><span>terdaftar</span></td><td><span>Rata-rata capaian</span><strong>{{ number_format($summary['average_percentage'], 1, ',', '.') }}%</strong><span>nilai akhir</span></td><td><span>Sudah tuntas</span><strong>{{ $summary['tuntas_count'] }}</strong><span>peserta</span></td><td><span>Belum tuntas</span><strong>{{ $summary['belum_tuntas_count'] }}</strong><span>peserta</span></td></tr></table>

    <h2 class="section-title">Capaian departemen</h2>
    <table class="department-table"><thead><tr><th>Departemen</th><th>Peserta</th><th>Rata-rata</th><th>Tuntas</th><th>Belum tuntas</th></tr></thead><tbody>@foreach($summary['departments'] as $department)<tr><td>{{ $department['department'] }}</td><td>{{ $department['participant_count'] }}</td><td>{{ number_format($department['average_percentage'], 1, ',', '.') }}%</td><td class="status-done">{{ $department['tuntas_count'] }}</td><td class="status-open">{{ $department['belum_tuntas_count'] }}</td></tr>@endforeach</tbody></table>

    <section class="rankings"><div><h3>3 capaian tertinggi</h3><ol>@foreach($highest as $row)<li>{{ $row['name'] }} — {{ number_format($row['final_percentage'], 1, ',', '.') }}%</li>@endforeach</ol></div><div><h3>3 capaian terendah</h3><ol>@foreach($lowest as $row)<li>{{ $row['name'] }} — {{ number_format($row['final_percentage'], 1, ',', '.') }}%</li>@endforeach</ol></div></section>

    <h2 class="section-title">Rekap nilai peserta</h2>
    <table class="report-table"><thead><tr><th>Santri Karya</th><th>Departemen / Tim</th><th>Level</th>@foreach($activityColumns as $activity)<th>{{ $activity['name'] }}</th>@endforeach<th>Total</th><th>Nilai akhir</th><th>Predikat</th><th>Status</th><th>Rekomendasi</th></tr></thead><tbody>@foreach($rows as $row)<tr><td><strong>{{ $row['name'] }}</strong><br><span class="muted">{{ $row['gender'] === 'akhwat' ? 'Akhwat' : 'Ikhwan' }}</span></td><td>{{ $row['department'] ?: '—' }}<br><span class="muted">{{ $row['team'] ?: '—' }}</span></td><td>{{ $row['level'] === 'leader' ? 'Leader' : 'Staff' }}</td>@foreach($row['activities'] as $activity)<td class="activity-value">{{ $activity ? number_format($activity['percentage'], 0, ',', '.') . '%' : '—' }}</td>@endforeach<td class="activity-value">{{ number_format($row['total_score'], 0, ',', '.') }}</td><td class="activity-value">{{ number_format($row['final_percentage'], 1, ',', '.') }}%</td><td class="activity-value">{{ $row['grade'] }}</td><td class="{{ $row['final_status'] === 'tuntas' ? 'status-done' : 'status-open' }}">{{ $row['final_status'] === 'tuntas' ? 'Tuntas' : 'Belum tuntas' }}</td><td>{{ $row['recommendation'] }}</td></tr>@endforeach</tbody></table>

    <p class="signature-location">{{ $signatureDate }}</p>
    <section class="signature-grid">@foreach($signatories as $signatory)<div><p>{{ $loop->first ? 'Disetujui oleh,' : 'Diketahui oleh,' }}</p><p>{{ $signatory['title'] }}</p>@if($signatory['signature_path'])<img class="signature-image" src="{{ $signatory['signature_path'] }}" alt="Tanda tangan digital {{ $signatory['title'] }}">@else<div class="signature-missing">TTD digital belum dikonfigurasi</div>@endif<p class="signature-name">{{ $signatory['name'] }}</p></div>@endforeach</section>
    <footer class="footer">Dokumen dibuat dari snapshot periode pada {{ $generatedAt }}. Nilai aktivitas dibatasi maksimal 100%; status akhir mengikuti ambang jabatan peserta.</footer>
</body>
</html>
