<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Dashboard Pengawasan</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        h2 { font-size: 14px; margin: 18px 0 8px; }
        .meta { color: #666; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background: #f5f5f5; }
        .cards { width: 100%; }
        .cards td { width: 33%; vertical-align: top; border: none; padding: 4px 8px 4px 0; }
        .card-label { color: #666; font-size: 10px; }
        .card-value { font-size: 16px; font-weight: bold; }
        .warning { background: #fff8e6; border: 1px solid #f0d58b; padding: 8px; margin-bottom: 6px; }
        .summary { background: #f0f3ff; border: 1px solid #c5cff5; padding: 10px; margin-bottom: 14px; line-height: 1.5; }
        .muted { color: #666; font-size: 10px; }
    </style>
</head>
<body>
    <h1>Dashboard Pengawasan</h1>
    <p class="meta">{{ \App\Support\KanwilContact::exportSourceLabel() }}, Dicetak: {{ $generatedAt }} | {{ $periodLabel }}</p>

    @if(!empty($executive['summary']['points']))
        <h2>Ringkasan Eksekutif, {{ $executive['summary']['period'] ?? $periodLabel }}</h2>
        <div class="summary">
            <ul style="margin:0;padding-left:16px;">
                @foreach ($executive['summary']['points'] as $point)
                    <li style="margin-bottom:4px;">
                        <strong>{{ $point['label'] }}:</strong> {{ $point['text'] }}
                    </li>
                @endforeach
            </ul>
        </div>
    @elseif(!empty($executive['summary_text']))
        <h2>Ringkasan Eksekutif</h2>
        <div class="summary">{{ $executive['summary_text'] }}</div>
    @endif

    @if(!empty($executive['completion_rates']))
        <h2>Indikator Penyelesaian</h2>
        <table>
            <thead>
                <tr>
                    <th>Indikator</th>
                    <th>Persentase</th>
                    <th>Detail</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($executive['completion_rates'] as $rate)
                    <tr>
                        <td>{{ $rate['label'] ?? '-' }}</td>
                        <td>{{ number_format($rate['percent'] ?? 0, 1) }}%</td>
                        <td>
                            @if(($rate['total'] ?? 0) > 0)
                                {{ number_format($rate['selesai'] ?? 0) }} dari {{ number_format($rate['total']) }}
                            @else
                                Belum ada data
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <h2>Ringkasan Utama</h2>
    <table class="cards">
        <tr>
            @foreach (array_slice($stats, 0, 6, true) as $card)
                <td>
                    <div class="card-label">{{ $card['label'] ?? '-' }}</div>
                    <div class="card-value">{{ $card['value'] ?? 0 }}</div>
                </td>
            @endforeach
        </tr>
    </table>

    @if(!empty($warnings))
        <h2>Peringatan Dini</h2>
        @foreach ($warnings as $warning)
            <div class="warning">
                <strong>{{ $warning['level'] ?? 'info' }}</strong><br>
                {{ $warning['message'] ?? '' }}
            </div>
        @endforeach
    @endif

    @if(!empty($executive['intervention_priorities']))
        <h2>Prioritas Intervensi</h2>
        <table>
            <thead>
                <tr>
                    <th>Urgensi</th>
                    <th>Penyelenggara</th>
                    <th>Kabupaten</th>
                    <th>Isu</th>
                </tr>
            </thead>
            <tbody>
                @foreach (array_slice($executive['intervention_priorities'], 0, 10) as $row)
                    <tr>
                        <td>{{ \App\Support\DashboardExecutive::urgencyLabel($row['urgency'] ?? 'medium') }}</td>
                        <td>{{ $row['travel'] ?? '-' }}</td>
                        <td>{{ $row['kabupaten'] ?? '-' }}</td>
                        <td>{{ $row['issue'] ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if(!empty($executive['kabupaten_scorecard']))
        <h2>Rekap per Kabupaten/Kota</h2>
        <table>
            <thead>
                <tr>
                    <th>Kabupaten/Kota</th>
                    <th>Penyelenggara</th>
                    <th>Pengawasan</th>
                    <th>Temuan Belum Selesai</th>
                    <th>Pengaduan</th>
                    <th>Skor Risiko</th>
                    <th>Pemberangkatan Menunggu</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($executive['kabupaten_scorecard'] as $row)
                    <tr>
                        <td>{{ $row['kabupaten'] ?? '-' }}</td>
                        <td>{{ $row['total_travel'] ?? 0 }}</td>
                        <td>{{ $row['pengawasan'] ?? 0 }}</td>
                        <td>{{ $row['temuan_aktif'] ?? 0 }}</td>
                        <td>{{ $row['pengaduan'] ?? 0 }}</td>
                        <td>{{ $row['avg_risk'] ?? 0 }}</td>
                        <td>{{ $row['bap_pending'] ?? 0 }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if(!empty($rankings['risk']))
        <h2>Travel Berisiko Tertinggi</h2>
        <table>
            <thead>
                <tr>
                    <th>Penyelenggara</th>
                    <th>Skor</th>
                    <th>Tingkat</th>
                </tr>
            </thead>
            <tbody>
                @foreach (array_slice($rankings['risk'], 0, 10) as $row)
                    <tr>
                        <td>{{ $row['travel'] ?? '-' }}</td>
                        <td>{{ $row['total_score'] ?? '-' }}</td>
                        <td>{{ \App\Enums\RiskLevel::labelFor($row['risk_level'] ?? null) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if(!empty($executive['coverage_gaps']))
        <h2>Travel Belum Diawasi (12 Bulan)</h2>
        <table>
            <thead>
                <tr>
                    <th>Penyelenggara</th>
                    <th>Kabupaten</th>
                    <th>Pemeriksaan Terakhir</th>
                </tr>
            </thead>
            <tbody>
                @foreach (array_slice($executive['coverage_gaps'], 0, 10) as $row)
                    <tr>
                        <td>{{ $row['travel'] ?? '-' }}</td>
                        <td>{{ $row['kabupaten'] ?? '-' }}</td>
                        <td>{{ $row['last_inspection'] ?? 'Belum pernah' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
