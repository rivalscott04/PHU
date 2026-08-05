@php
    $recentPendingBap = $recentPendingBap ?? [];
    $pendingRegistrations = $pendingRegistrations ?? [];
    $openPengaduan = $openPengaduan ?? [];
    $highRiskTravels = $highRiskTravels ?? [];
    $upcomingDepartures = $upcomingDepartures ?? [];
@endphp

<div class="row mb-4">
    <div class="col-lg-7 mb-3 mb-lg-0">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">BA Menunggu Tindakan</h5>
                    <small class="text-muted">Pengajuan keberangkatan terbaru di seluruh NTB</small>
                </div>
                <a href="{{ route('bap') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                @if (! empty($recentPendingBap))
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Travel</th>
                                    <th>Kab/Kota</th>
                                    <th>Berangkat</th>
                                    <th>Status</th>
                                    <th class="text-end pe-3"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentPendingBap as $item)
                                    <tr>
                                        <td class="ps-3">
                                            <span class="d-block">{{ $item['travel_name'] }}</span>
                                            <small class="text-muted">{{ $item['people'] }} jamaah</small>
                                        </td>
                                        <td>{{ $item['kabupaten'] }}</td>
                                        <td>{{ $item['datetime'] }}</td>
                                        <td>
                                            <span class="badge {{ $item['badge_class'] }}">
                                                {{ $item['badge_label'] }}
                                            </span>
                                        </td>
                                        <td class="text-end pe-3">
                                            <a href="{{ $item['url'] }}" class="btn btn-sm btn-primary">Proses</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5 px-3">
                        <i class="bx bx-check-circle text-success fs-1 d-block mb-2"></i>
                        <p class="text-muted mb-0">Tidak ada BA yang menunggu tindakan saat ini.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">Registrasi Menunggu</h5>
                    <small class="text-muted">PPIU baru perlu verifikasi Kanwil</small>
                </div>
                <a href="{{ route('travel', ['filter' => 'pending']) }}" class="btn btn-sm btn-outline-primary">Lihat</a>
            </div>
            <div class="card-body">
                @if (! empty($pendingRegistrations))
                    <div class="list-group list-group-flush">
                        @foreach ($pendingRegistrations as $item)
                            <a href="{{ $item['url'] }}" class="list-group-item list-group-item-action px-0">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div>
                                        <div class="fw-medium">{{ $item['name'] }}</div>
                                        <small class="text-muted">{{ $item['kabupaten'] }} · {{ $item['created_at'] }}</small>
                                    </div>
                                    <span class="badge bg-warning text-dark">Pending</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="bx bx-user-check text-muted fs-3 d-block mb-2"></i>
                        <p class="text-muted mb-0 small">Tidak ada registrasi menunggu verifikasi.</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">Pengaduan Terbuka</h5>
                    <small class="text-muted">Belum selesai diproses</small>
                </div>
                <a href="{{ route('pengaduan') }}" class="btn btn-sm btn-outline-primary">Lihat</a>
            </div>
            <div class="card-body">
                @if (! empty($openPengaduan))
                    <div class="list-group list-group-flush">
                        @foreach ($openPengaduan as $item)
                            <a href="{{ $item['url'] }}" class="list-group-item list-group-item-action px-0">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div>
                                        <div class="fw-medium">{{ $item['travel_name'] }}</div>
                                        <small class="text-muted">{{ $item['subject'] }}</small>
                                    </div>
                                    <span class="badge {{ $item['badge_class'] }}">{{ $item['badge_label'] }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-3">
                        <i class="bx bx-message-square-check text-muted fs-3 d-block mb-2"></i>
                        <p class="text-muted mb-0 small">Tidak ada pengaduan terbuka.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-lg-6 mb-3 mb-lg-0">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">Travel Risiko Tinggi</h5>
                    <small class="text-muted">PPIU berstatus HIGH atau CRITICAL</small>
                </div>
                @if(\App\Support\RouteAccess::canAccessRoute(auth()->user(), 'v2.risk.index'))
                    <a href="{{ route('v2.risk.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                @endif
            </div>
            <div class="card-body p-0">
                @if (! empty($highRiskTravels))
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Penyelenggara</th>
                                    <th>Kab/Kota</th>
                                    <th>Skor</th>
                                    <th>Risiko</th>
                                    <th class="text-end pe-3"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($highRiskTravels as $item)
                                    <tr>
                                        <td class="ps-3 fw-medium">{{ $item['travel_name'] }}</td>
                                        <td>{{ $item['kabupaten'] }}</td>
                                        <td>{{ number_format($item['score'], 0) }}</td>
                                        <td>
                                            <span class="badge {{ $item['badge_class'] }}">{{ $item['risk_label'] }}</span>
                                        </td>
                                        <td class="text-end pe-3">
                                            <a href="{{ $item['url'] }}" class="btn btn-sm btn-light">Detail</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4 px-3">
                        <i class="bx bx-shield-quarter text-success fs-3 d-block mb-2"></i>
                        <p class="text-muted mb-0 small">Tidak ada travel berisiko tinggi saat ini.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">Keberangkatan Terdekat</h5>
                    <small class="text-muted">Jadwal disetujui mendatang di NTB</small>
                </div>
                <a href="{{ route('keberangkatan') }}" class="btn btn-sm btn-outline-primary">Kalender</a>
            </div>
            <div class="card-body">
                @if (! empty($upcomingDepartures))
                    <div class="list-group list-group-flush">
                        @foreach ($upcomingDepartures as $departure)
                            <a href="{{ $departure['url'] }}" class="list-group-item list-group-item-action px-0">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div>
                                        <div class="fw-medium">{{ $departure['travel_name'] }}</div>
                                        <small class="text-muted">
                                            {{ $departure['package'] }} · {{ $departure['datetime'] }}
                                        </small>
                                    </div>
                                    <span class="badge bg-primary-subtle text-primary">
                                        {{ $departure['people'] }} jamaah
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bx bx-calendar-event text-muted fs-3 d-block mb-2"></i>
                        <p class="text-muted mb-0 small">Belum ada jadwal keberangkatan mendatang.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
