@php
    $travel = $travel ?? null;
    $old = fn (string $key, mixed $default = '') => old($key, $travel?->{$key} ?? $default);
@endphp

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="Penyelenggara" class="form-label">Nama Penyelenggara @include('partials.required-star')</label>
        <input type="text" class="form-control @error('Penyelenggara') is-invalid @enderror"
            id="Penyelenggara" name="Penyelenggara" value="{{ $old('Penyelenggara') }}"
            placeholder="Contoh: PT. Contoh Travel Sejahtera" required>
        @error('Penyelenggara')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <small class="text-muted">Sesuai nama pada izin resmi</small>
    </div>

    <div class="col-md-6 mb-3">
        <label for="Status" class="form-label">Jenis Izin @include('partials.required-star')</label>
        <select class="form-select @error('Status') is-invalid @enderror" id="Status" name="Status" required>
            <option value="">Pilih jenis izin</option>
            <option value="PPIU" @selected($old('Status') === 'PPIU')>PPIU: Umrah saja</option>
            <option value="PIHK" @selected($old('Status') === 'PIHK')>PIHK: Haji, Umrah &amp; Haji Khusus</option>
        </select>
        @error('Status')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="Pusat" class="form-label">No. SK / NIB Pusat @include('partials.required-star')</label>
        <input type="text" class="form-control @error('Pusat') is-invalid @enderror"
            id="Pusat" name="Pusat" value="{{ $old('Pusat') }}" required>
        @error('Pusat')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="Tanggal" class="form-label">Tanggal SK @include('partials.required-star')</label>
        <input type="date" class="form-control @error('Tanggal') is-invalid @enderror"
            id="Tanggal" name="Tanggal" value="{{ $old('Tanggal') }}" required>
        @error('Tanggal')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="nilai_akreditasi" class="form-label">Nilai Akreditasi @include('partials.required-star')</label>
        <select class="form-select @error('nilai_akreditasi') is-invalid @enderror"
            id="nilai_akreditasi" name="nilai_akreditasi" required>
            <option value="">Pilih</option>
            @foreach (['A', 'B', 'C'] as $grade)
                <option value="{{ $grade }}" @selected($old('nilai_akreditasi') === $grade)>{{ $grade }}</option>
            @endforeach
        </select>
        @error('nilai_akreditasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="tanggal_akreditasi" class="form-label">Tanggal Akreditasi @include('partials.required-star')</label>
        <input type="date" class="form-control @error('tanggal_akreditasi') is-invalid @enderror"
            id="tanggal_akreditasi" name="tanggal_akreditasi" value="{{ $old('tanggal_akreditasi') }}" required>
        @error('tanggal_akreditasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="lembaga_akreditasi" class="form-label">Lembaga Akreditasi @include('partials.required-star')</label>
        <input type="text" class="form-control @error('lembaga_akreditasi') is-invalid @enderror"
            id="lembaga_akreditasi" name="lembaga_akreditasi" value="{{ $old('lembaga_akreditasi', 'Kementerian Haji dan Umroh') }}" required>
        @error('lembaga_akreditasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="Pimpinan" class="form-label">Nama Pimpinan / Direktur @include('partials.required-star')</label>
        <input type="text" class="form-control @error('Pimpinan') is-invalid @enderror"
            id="Pimpinan" name="Pimpinan" value="{{ $old('Pimpinan') }}" required>
        @error('Pimpinan')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="Telepon" class="form-label">Telepon Kantor @include('partials.required-star')</label>
        <input type="text" class="form-control @error('Telepon') is-invalid @enderror"
            id="Telepon" name="Telepon" value="{{ $old('Telepon') }}" placeholder="0370-123456" required>
        @error('Telepon')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="kab_kota" class="form-label">Kabupaten / Kota @include('partials.required-star')</label>
        <select class="form-select @error('kab_kota') is-invalid @enderror" id="kab_kota" name="kab_kota" required>
            <option value="">Pilih kabupaten/kota</option>
            @foreach ($kabupatens as $kab)
                <option value="{{ $kab }}" @selected($old('kab_kota') === $kab)>{{ $kab }}</option>
            @endforeach
        </select>
        @error('kab_kota')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="alamat_kantor_lama" class="form-label">Alamat Kantor (Lama) @include('partials.required-star')</label>
        <textarea class="form-control @error('alamat_kantor_lama') is-invalid @enderror"
            id="alamat_kantor_lama" name="alamat_kantor_lama" rows="2" required>{{ $old('alamat_kantor_lama') }}</textarea>
        @error('alamat_kantor_lama')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-12 mb-3">
        <label for="alamat_kantor_baru" class="form-label">Alamat Kantor (Saat Ini) @include('partials.required-star')</label>
        <textarea class="form-control @error('alamat_kantor_baru') is-invalid @enderror"
            id="alamat_kantor_baru" name="alamat_kantor_baru" rows="2" required>{{ $old('alamat_kantor_baru') }}</textarea>
        @error('alamat_kantor_baru')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <small class="text-muted">Isi sama dengan alamat lama jika belum pindah</small>
    </div>
</div>
