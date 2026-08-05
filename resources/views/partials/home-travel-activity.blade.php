@php
    $activeBap = $activeBap ?? [];
    $upcomingDepartures = $upcomingDepartures ?? [];
@endphp

<div class="row mb-4">
    <div class="col-lg-7 mb-3 mb-lg-0">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">BA Pemberangkatan Aktif</h5>
                    <small class="text-muted">Pengajuan terbaru dan statusnya</small>
                </div>
                <a href="{{ route('bap') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                @if (! empty($activeBap))
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Paket</th>
                                    <th>Jamaah</th>
                                    <th>Berangkat</th>
                                    <th>Status</th>
                                    <th class="text-end pe-3"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($activeBap as $item)
                                    <tr>
                                        <td class="ps-3">{{ $item['package'] }}</td>
                                        <td>{{ $item['people'] }}</td>
                                        <td>{{ $item['datetime'] }}</td>
                                        <td>
                                            <span class="badge {{ $item['badge_class'] }}">
                                                {{ $item['badge_label'] }}
                                            </span>
                                        </td>
                                        <td class="text-end pe-3">
                                            <a href="{{ $item['url'] }}" class="btn btn-sm btn-light">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5 px-3">
                        <i class="bx bx-file text-muted fs-1 d-block mb-2"></i>
                        <p class="text-muted mb-3">Belum ada pengajuan BA Pemberangkatan.</p>
                        <a href="{{ route('form.bap') }}" class="btn btn-sm btn-primary">
                            <i class="bx bx-plus me-1"></i> Buat Pengajuan
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">Keberangkatan Terdekat</h5>
                    <small class="text-muted">Jadwal yang sudah disetujui</small>
                </div>
                <a href="{{ route('keberangkatan') }}" class="btn btn-sm btn-outline-primary">Kalender</a>
            </div>
            <div class="card-body">
                @if (! empty($upcomingDepartures))
                    <div class="list-group list-group-flush">
                        @foreach ($upcomingDepartures as $departure)
                            <a href="{{ $departure['url'] }}"
                               class="list-group-item list-group-item-action px-0">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div>
                                        <div class="fw-medium">{{ $departure['package'] }}</div>
                                        <small class="text-muted">
                                            {{ $departure['datetime'] }}
                                            @if ($departure['airlines'])
                                                · {{ $departure['airlines'] }}
                                            @endif
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
                        <i class="bx bx-calendar-event text-muted fs-1 d-block mb-2"></i>
                        <p class="text-muted mb-0 small">
                            Belum ada jadwal keberangkatan mendatang.
                            Jadwal muncul setelah BA Pemberangkatan disetujui.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
