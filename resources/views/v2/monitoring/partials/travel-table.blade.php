@php
    use App\Enums\TravelRegistrationStatus;

    $travels = $travels ?? collect();
    $compact = $compact ?? false;

    $riskBadges = [
        'LOW' => 'success',
        'MEDIUM' => 'info',
        'HIGH' => 'warning',
        'CRITICAL' => 'danger',
    ];
    $riskLabels = [
        'LOW' => 'Rendah',
        'MEDIUM' => 'Sedang',
        'HIGH' => 'Tinggi',
        'CRITICAL' => 'Kritis',
    ];
@endphp

<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <th class="ps-3">Travel</th>
                <th>Kabupaten</th>
                <th>Jenis</th>
                @unless($compact)
                    <th>Registrasi</th>
                @endunless
                <th>Terakhir Diawasi</th>
                <th>Pengawasan</th>
                <th>Pengaduan</th>
                <th>BA Pending</th>
                <th class="pe-3">Risiko</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($travels as $travel)
                @php
                    $risk = $travel->riskScore?->risk_level?->value ?? $travel->riskScore?->risk_level;
                    $regStatus = $travel->registration_status ?? TravelRegistrationStatus::Approved;
                    $lastInspection = $travel->last_inspection_at
                        ? \Carbon\Carbon::parse($travel->last_inspection_at)->format('d/m/Y')
                        : null;
                    $bapPending = (int) ($travel->bap_pending_count ?? 0);
                @endphp
                <tr>
                    <td class="ps-3 fw-medium">{{ $travel->Penyelenggara }}</td>
                    <td class="text-muted">{{ $travel->kab_kota }}</td>
                    <td><span class="badge bg-light text-dark border">{{ $travel->Status }}</span></td>
                    @unless($compact)
                        <td>
                            @if ($regStatus !== TravelRegistrationStatus::Approved)
                                <span class="badge {{ $regStatus->badgeClass() }}">{{ $regStatus->label() }}</span>
                            @else
                                <span class="text-muted small">Disetujui</span>
                            @endif
                        </td>
                    @endunless
                    <td>
                        @if ($lastInspection)
                            {{ $lastInspection }}
                        @else
                            <span class="text-muted">Belum pernah</span>
                        @endif
                    </td>
                    <td>{{ number_format($travel->inspections_count) }}</td>
                    <td>@include('v2.partials.pengaduan-count', ['travel' => $travel])</td>
                    <td>
                        @if ($bapPending > 0)
                            <span class="badge bg-warning text-dark">{{ $bapPending }}</span>
                        @else
                            <span class="text-muted">0</span>
                        @endif
                    </td>
                    <td class="pe-3">
                        @if($risk)
                            <span class="badge bg-{{ $riskBadges[$risk] ?? 'secondary' }}">
                                {{ $riskLabels[$risk] ?? $risk }}
                            </span>
                        @else
                            <span class="text-muted">Tidak ada</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $compact ? 8 : 9 }}" class="text-center text-muted py-5">
                        <i class="bx bx-buildings d-block mb-2" style="font-size:2rem;"></i>
                        Belum ada data travel.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
