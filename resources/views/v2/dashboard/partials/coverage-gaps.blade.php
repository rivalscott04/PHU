@php
    $gaps = $executive['coverage_gaps'] ?? [];
@endphp

<div class="card border-0 shadow-sm mb-3" id="v2-coverage-gaps">
    <div class="card-header bg-transparent border-bottom">
        <h5 class="mb-0 fw-semibold">Travel Belum Diawasi (12 Bulan)</h5>
        <small class="text-muted">Penyelenggara tanpa pemeriksaan dalam 12 bulan terakhir</small>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Penyelenggara</th>
                        <th>Kabupaten</th>
                        <th>Pemeriksaan Terakhir</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody id="coverage-gaps-body">
                    @forelse ($gaps as $row)
                        <tr>
                            <td class="ps-3 fw-medium">{{ $row['travel'] ?? '-' }}</td>
                            <td>{{ $row['kabupaten'] ?? '-' }}</td>
                            <td>{{ $row['last_inspection'] ?? 'Belum pernah' }}</td>
                            <td>
                                @if($row['last_inspection'] ?? null)
                                    <span class="text-warning">{{ $row['months_ago'] ?? '-' }} bulan lalu</span>
                                @else
                                    <span class="badge bg-danger">Belum pernah diawasi</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">Semua travel telah diawasi dalam 12 bulan terakhir.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
