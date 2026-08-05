@php
    $data = $data ?? null;
@endphp

@if ($data)
    <h6 class="text-muted text-uppercase small mb-3">Data pengaju</h6>
    <dl class="row mb-4">
        <dt class="col-sm-4 text-muted fw-normal">Nama</dt>
        <dd class="col-sm-8 mb-2">{{ $data->name }}</dd>
        <dt class="col-sm-4 text-muted fw-normal">Jabatan</dt>
        <dd class="col-sm-8 mb-2">{{ $data->jabatan }}</dd>
        <dt class="col-sm-4 text-muted fw-normal">PPIU</dt>
        <dd class="col-sm-8 mb-2">{{ $data->ppiuname }}</dd>
        <dt class="col-sm-4 text-muted fw-normal">Nomor HP</dt>
        <dd class="col-sm-8 mb-2">{{ $data->address_phone }}</dd>
        <dt class="col-sm-4 text-muted fw-normal">Kab/Kota</dt>
        <dd class="col-sm-8 mb-2">{{ $data->kab_kota }}</dd>
    </dl>

    <h6 class="text-muted text-uppercase small mb-3">Data keberangkatan</h6>
    <dl class="row mb-3">
        <dt class="col-sm-4 text-muted fw-normal">Jumlah Jamaah</dt>
        <dd class="col-sm-8 mb-2">{{ $data->people }} orang</dd>
        <dt class="col-sm-4 text-muted fw-normal">Durasi</dt>
        <dd class="col-sm-8 mb-2">{{ $data->days }} hari</dd>
        <dt class="col-sm-4 text-muted fw-normal">Harga per Orang</dt>
        <dd class="col-sm-8 mb-2">Rp {{ number_format($data->price, 0, ',', '.') }}</dd>
        <dt class="col-sm-4 text-muted fw-normal">Berangkat</dt>
        <dd class="col-sm-8 mb-2">{{ \Carbon\Carbon::parse($data->datetime)->translatedFormat('d F Y') }}</dd>
        <dt class="col-sm-4 text-muted fw-normal">Maskapai berangkat</dt>
        <dd class="col-sm-8 mb-2">{{ $data->airlines }}</dd>
        <dt class="col-sm-4 text-muted fw-normal">Pulang</dt>
        <dd class="col-sm-8 mb-2">{{ \Carbon\Carbon::parse($data->returndate)->translatedFormat('d F Y') }}</dd>
        <dt class="col-sm-4 text-muted fw-normal">Maskapai pulang</dt>
        <dd class="col-sm-8 mb-2">{{ $data->airlines2 }}</dd>
    </dl>

    @if ($data->jamaah?->isNotEmpty())
        <h6 class="text-muted text-uppercase small mb-2">Daftar jamaah</h6>
        @include('travel.partials.bap-jamaah-list', [
            'jamaah' => $data->jamaah,
            'maxHeight' => $jamaahMaxHeight ?? '200px',
        ])
    @endif
@endif
