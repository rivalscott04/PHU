@php
    $variant = $variant ?? 'pemberangkatan';
@endphp

@if ($variant === 'pemberangkatan')
    <div class="alert alert-info border-0 mb-3" role="note">
        <div class="d-flex gap-2">
            <i class="bx bx-info-circle fs-5 flex-shrink-0 mt-1"></i>
            <div class="small mb-0">
                <strong>BA Pemberangkatan</strong>, Berita Acara Pelaporan Keberangkatan jamaah.
                Travel membuat pengajuan, melampirkan surat pernyataan, lalu mengajukan ke Kanwil.
                <strong>Persetujuan dilakukan oleh Admin atau Kabupaten</strong> (bukan Pimpinan).
                Setelah disetujui, dokumen dapat dicetak dengan e sign dan jadwal muncul di menu Jadwal Keberangkatan.
            </div>
        </div>
    </div>
@elseif ($variant === 'pemeriksaan')
    <div class="alert alert-info border-0 mb-3" role="note">
        <div class="d-flex gap-2">
            <i class="bx bx-info-circle fs-5 flex-shrink-0 mt-1"></i>
            <div class="small mb-0">
                <strong>BA Pemeriksaan</strong>, Berita Acara hasil pemeriksaan pengawasan PPIU.
                Pengawas atau Admin menjadwalkan inspeksi, mencatat temuan, dan memverifikasi tindak lanjut travel.
                Modul ini <strong>terpisah dari BA Pemberangkatan</strong> (persetujuan keberangkatan jamaah).
            </div>
        </div>
    </div>
@endif
