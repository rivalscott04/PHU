<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Pengawasan</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        .meta { color: #666; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        th { background: #f5f5f5; }
    </style>
</head>
<body>
    <h1>Laporan Pengawasan</h1>
    <p class="meta">Dicetak: {{ $generatedAt }} | {{ $filterSummary }}</p>

    <table>
        <thead>
            <tr>
                <th>No Pengawasan</th>
                <th>Penyelenggara</th>
                <th>Tanggal</th>
                <th>Jenis</th>
                <th>Status</th>
                <th>Jumlah Temuan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($inspections as $inspection)
                <tr>
                    <td>{{ $inspection->inspection_no }}</td>
                    <td>{{ $inspection->travel?->Penyelenggara }}</td>
                    <td>{{ $inspection->inspection_date?->format('d/m/Y') }}</td>
                    <td>{{ $inspection->inspection_type?->value ?? $inspection->inspection_type }}</td>
                    <td>{{ $inspection->status?->value ?? $inspection->status }}</td>
                    <td>{{ $inspection->findings_count ?? 0 }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center;">Tidak ada data pengawasan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
