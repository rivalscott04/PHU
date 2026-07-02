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
    </style>
</head>
<body>
    <h1>Dashboard Pengawasan V2</h1>
    <p class="meta">Dicetak: {{ $generatedAt }} | {{ $periodLabel }}</p>

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
                        <td>{{ $row['risk_level'] ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
