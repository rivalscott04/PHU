@props(['jamaah', 'canUpdateStatus' => false])

@php
    $context = [
        'id' => $jamaah->id,
        'nama' => $jamaah->nama_lengkap,
        'nik' => $jamaah->no_ktp,
        'paspor' => $jamaah->no_paspor ?? '-',
        'travel' => $jamaah->travel->Penyelenggara ?? 'PIHK Tidak Diketahui',
        'kabupaten' => $jamaah->travel->kab_kota ?? '-',
        'jenisKelamin' => $jamaah->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
        'spph' => $jamaah->nomor_porsi ?? '-',
        'statusPendaftaran' => $jamaah->getStatusText(),
        'buktiSetor' => $jamaah->getBuktiSetorStatusText(),
    ];
@endphp

<span class="badge {{ $jamaah->getStatusBadgeClass() }}">{{ $jamaah->getStatusText() }}</span>

@if($canUpdateStatus && $jamaah->canKanwilApprove())
    <br>
    <button type="button"
        class="btn btn-sm btn-warning mt-1 btn-ubah-status-pendaftaran"
        @foreach($context as $key => $value) data-{{ \Illuminate\Support\Str::kebab($key) }}="{{ $value }}" @endforeach
        onclick="ubahStatusPendaftaran(this)">
        <i class="bx bx-check-circle me-1"></i>Setujui Pendaftaran
    </button>
@elseif($canUpdateStatus && $jamaah->status_pendaftaran === 'pending')
    <br>
    <small class="text-muted d-block mt-1">Menunggu verifikasi kabupaten &amp; SPPH</small>
@endif
