@props(['jamaah', 'canVerifyBukti' => false, 'canAssignSpph' => false])

@php
    $spphContext = [
        'id' => $jamaah->id,
        'nama' => $jamaah->nama_lengkap,
        'nik' => $jamaah->no_ktp,
        'paspor' => $jamaah->no_paspor ?? '-',
        'travel' => $jamaah->travel->Penyelenggara ?? 'PIHK Tidak Diketahui',
        'kabupaten' => $jamaah->travel->kab_kota ?? '-',
        'jenisKelamin' => $jamaah->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
    ];
@endphp

<span class="badge {{ $jamaah->getBuktiSetorStatusBadgeClass() }}">{{ $jamaah->getBuktiSetorStatusText() }}</span>

@if($canVerifyBukti && $jamaah->bukti_setor_bank && $jamaah->status_verifikasi_bukti !== 'verified')
    <br>
    <button type="button"
        class="btn btn-sm btn-primary mt-1 btn-verify-bukti-setor"
        @foreach($spphContext as $key => $value) data-{{ \Illuminate\Support\Str::kebab($key) }}="{{ $value }}" @endforeach
        onclick="verifikasiBuktiSetor(this)">
        <i class="bx bx-check-shield me-1"></i>Verifikasi
    </button>
@endif

@if($canAssignSpph && $jamaah->canAssignPorsiNumber())
    <br>
    <button type="button"
        class="btn btn-sm btn-success mt-1 btn-tetapkan-spph"
        @foreach($spphContext as $key => $value) data-{{ \Illuminate\Support\Str::kebab($key) }}="{{ $value }}" @endforeach
        onclick="tetapkanSppH(this)">
        <i class="bx bx-id-card me-1"></i>Tetapkan SPPH
    </button>
@endif
