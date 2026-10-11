{{-- Nomor dan tanggal berkas cabang untuk berita acara. Boleh dikosongkan petugas
     dan dilengkapi menyusul; yang kosong tercetak titik-titik. --}}
<h6 class="mt-2 mb-1">Data Berkas Cabang</h6>
<p class="text-muted small mb-3">Dipakai untuk mengisi berita acara pembukaan cabang. Boleh dilengkapi menyusul.</p>
<div class="row">
    @foreach (\App\Models\CabangTravel::detailDokumen() as $name => $field)
        @php($nilai = $cabang?->{$name})
        <div class="col-md-6 mb-3">
            <label for="{{ $name }}" class="form-label">{{ $field['label'] }}</label>
            <input type="{{ $field['type'] }}" class="form-control @error($name) is-invalid @enderror" id="{{ $name }}" name="{{ $name }}"
                value="{{ old($name, $field['type'] === 'date' ? $nilai?->format('Y-m-d') : $nilai) }}"
                @if ($name === 'nik_kepala') inputmode="numeric" maxlength="16" @endif>
            @error($name)<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    @endforeach
</div>
