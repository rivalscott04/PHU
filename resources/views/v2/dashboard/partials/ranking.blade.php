<div class="row" id="v2-dashboard-rankings">
    <div class="col-lg-6 mb-3">
        <div class="card h-100">
            <div class="card-header"><h5 class="mb-0">Top 10 Travel Risiko Tinggi</h5></div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead><tr><th>Travel</th><th>Skor</th><th>Level</th></tr></thead>
                    <tbody>
                        @forelse ($rankings['risk'] ?? [] as $row)
                            <tr>
                                <td>
                                    @if (!empty($row['travel_id']) && \App\Support\RouteAccess::canAccessRoute(auth()->user(), 'v2.risk.show'))
                                        <a href="{{ route('v2.risk.show', $row['travel_id']) }}">{{ $row['travel'] }}</a>
                                    @else
                                        {{ $row['travel'] ?? '-' }}
                                    @endif
                                </td>
                                <td>{{ $row['total_score'] ?? 0 }}</td>
                                <td><span class="badge bg-warning">{{ $row['risk_level'] ?? '-' }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted">Belum ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-3">
        <div class="card h-100">
            <div class="card-header"><h5 class="mb-0">Top 10 Travel Jamaah Terbanyak</h5></div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead><tr><th>Travel</th><th>Jumlah</th></tr></thead>
                    <tbody>
                        @forelse ($rankings['jamaah'] ?? [] as $row)
                            <tr>
                                <td>{{ $row['travel'] ?? '-' }}</td>
                                <td>{{ $row['total'] ?? 0 }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-center text-muted">Belum ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-3">
        <div class="card h-100">
            <div class="card-header"><h5 class="mb-0">Top 10 Travel Pengaduan</h5></div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead><tr><th>Travel</th><th>Jumlah</th></tr></thead>
                    <tbody>
                        @forelse ($rankings['pengaduan'] ?? [] as $row)
                            <tr>
                                <td>{{ $row['travel'] ?? '-' }}</td>
                                <td>
                                    @include('v2.partials.pengaduan-count', [
                                        'travelId' => $row['travel_id'] ?? null,
                                        'travelName' => $row['travel'] ?? null,
                                        'count' => $row['total'] ?? 0,
                                    ])
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-center text-muted">Belum ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6 mb-3">
        <div class="card h-100">
            <div class="card-header"><h5 class="mb-0">Top 10 Kabupaten Aktif</h5></div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead><tr><th>Kabupaten</th><th>Pengawasan</th></tr></thead>
                    <tbody>
                        @forelse ($rankings['kabupaten'] ?? [] as $row)
                            <tr>
                                <td>{{ $row['kabupaten'] ?? '-' }}</td>
                                <td>{{ $row['total'] ?? 0 }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-center text-muted">Belum ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
