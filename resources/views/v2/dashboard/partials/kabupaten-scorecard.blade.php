@php
    $scorecard = $executive['kabupaten_scorecard'] ?? [];
@endphp

<div class="card border-0 shadow-sm mb-3" id="v2-kabupaten-scorecard">
    <div class="card-header bg-transparent border-bottom">
        <h5 class="mb-0 fw-semibold">Rekap per Kabupaten/Kota</h5>
        <small class="text-muted">Perbandingan kinerja pengawasan per wilayah pada periode terpilih</small>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Kabupaten/Kota</th>
                        <th title="Jumlah penyelenggara travel umrah/haji terdaftar">Penyelenggara</th>
                        <th title="Jumlah pemeriksaan pengawasan pada periode ini">Pengawasan</th>
                        <th title="Temuan pemeriksaan yang belum diselesaikan">Temuan Belum Selesai</th>
                        <th title="Jumlah pengaduan jamaah/masyarakat">Pengaduan</th>
                        <th title="Rata-rata skor risiko penyelenggara (skala 0 sampai 100)">Skor Risiko</th>
                        <th title="Pengajuan berita acara pemberangkatan yang menunggu persetujuan">Pemberangkatan Menunggu</th>
                    </tr>
                </thead>
                <tbody id="kabupaten-scorecard-body">
                    @forelse ($scorecard as $row)
                        <tr>
                            <td class="ps-3 fw-medium">{{ $row['kabupaten'] ?? '-' }}</td>
                            <td>{{ $row['total_travel'] ?? 0 }}</td>
                            <td>{{ $row['pengawasan'] ?? 0 }}</td>
                            <td>
                                @if(($row['temuan_aktif'] ?? 0) > 0)
                                    <span class="badge bg-warning text-dark">{{ $row['temuan_aktif'] }}</span>
                                @else
                                    {{ $row['temuan_aktif'] ?? 0 }}
                                @endif
                            </td>
                            <td>{{ $row['pengaduan'] ?? 0 }}</td>
                            <td>{{ $row['avg_risk'] ?? 0 }}</td>
                            <td>{{ $row['bap_pending'] ?? 0 }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada data rekap wilayah untuk filter ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
