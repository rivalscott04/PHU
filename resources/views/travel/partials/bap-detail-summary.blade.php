@php
    $data = $data ?? null;
@endphp

@if ($data)
    <h6 class="text-muted text-uppercase small mb-3">Data pengaju</h6>
    <dl class="row small mb-4">
        <dt class="col-sm-4">Nama</dt>
        <dd class="col-sm-8">{{ $data->name }}</dd>
        <dt class="col-sm-4">Jabatan</dt>
        <dd class="col-sm-8">{{ $data->jabatan }}</dd>
        <dt class="col-sm-4">PPIU</dt>
        <dd class="col-sm-8">{{ $data->ppiuname }}</dd>
        <dt class="col-sm-4">Nomor HP</dt>
        <dd class="col-sm-8">{{ $data->address_phone }}</dd>
        <dt class="col-sm-4">Kab/Kota</dt>
        <dd class="col-sm-8">{{ $data->kab_kota }}</dd>
    </dl>

    <h6 class="text-muted text-uppercase small mb-3">Data keberangkatan</h6>
    <dl class="row small mb-3">
        <dt class="col-sm-4">Jumlah Jamaah</dt>
        <dd class="col-sm-8">{{ $data->people }} orang</dd>
        <dt class="col-sm-4">Durasi</dt>
        <dd class="col-sm-8">{{ $data->days }} hari</dd>
        <dt class="col-sm-4">Harga per Orang</dt>
        <dd class="col-sm-8">Rp {{ number_format($data->price, 0, ',', '.') }}</dd>
        <dt class="col-sm-4">Berangkat</dt>
        <dd class="col-sm-8">{{ \Carbon\Carbon::parse($data->datetime)->translatedFormat('d F Y') }}</dd>
        <dt class="col-sm-4">Maskapai berangkat</dt>
        <dd class="col-sm-8">{{ $data->airlines }}</dd>
        <dt class="col-sm-4">Pulang</dt>
        <dd class="col-sm-8">{{ \Carbon\Carbon::parse($data->returndate)->translatedFormat('d F Y') }}</dd>
        <dt class="col-sm-4">Maskapai pulang</dt>
        <dd class="col-sm-8">{{ $data->airlines2 }}</dd>
    </dl>

    @if ($data->jamaah?->isNotEmpty())
        <h6 class="text-muted text-uppercase small mb-2">Daftar jamaah</h6>
        @include('travel.partials.bap-jamaah-list', [
            'jamaah' => $data->jamaah,
            'maxHeight' => $jamaahMaxHeight ?? '200px',
        ])
    @endif
@endif
