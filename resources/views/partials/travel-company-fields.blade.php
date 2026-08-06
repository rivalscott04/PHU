@php
    $travel = $travel ?? null;
    $section = $section ?? 'all';
    $compact = $compact ?? false;
    $colClass = $compact ? 'col-12 col-lg-8 mx-auto' : 'col-md-6';
    $colAkreditasi = $compact ? $colClass : 'col-md-4';
    $colAlamatFull = $compact ? 'col-12 col-lg-8 mx-auto' : 'col-md-12';
    $colAlamatHalf = $compact ? 'col-12 col-lg-8 mx-auto' : 'col-md-6';
    $inputClass = $compact ? 'form-control form-control-lg' : 'form-control';
    $selectClass = $compact ? 'form-select form-select-lg' : 'form-select';
    $fieldSpacing = $compact ? 'mb-4' : 'mb-3';

    $old = function (string $key, mixed $default = '') use ($travel) {
        if (old($key) !== null) {
            return old($key);
        }

        $value = $travel?->{$key} ?? $default;

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        return $value;
    };

    $show = fn (string ...$names) => $section === 'all' || in_array($section, $names, true);
@endphp

<div class="row">
    @if ($show('profil'))
        <div class="{{ $colClass }} {{ $fieldSpacing }}">
            <label for="Penyelenggara" class="form-label">Nama Penyelenggara @include('partials.required-star')</label>
            <input type="text" class="{{ $inputClass }} @error('Penyelenggara') is-invalid @enderror"
                id="Penyelenggara" name="Penyelenggara" value="{{ $old('Penyelenggara') }}"
                placeholder="Contoh: PT. Contoh Travel Sejahtera" required>
            @error('Penyelenggara')<div class="invalid-feedback">{{ $message }}</div>@enderror
            @if ($compact)
                <div class="form-text">Sesuai nama pada izin resmi travel Anda</div>
            @else
                <small class="text-muted">Sesuai nama pada izin resmi</small>
            @endif
        </div>

        <div class="{{ $colClass }} {{ $fieldSpacing }}">
            <label for="Status" class="form-label">Jenis Izin @include('partials.required-star')</label>
            <select class="{{ $selectClass }} @error('Status') is-invalid @enderror" id="Status" name="Status" required>
                <option value="">Pilih jenis izin</option>
                <option value="PPIU" @selected($old('Status') === 'PPIU')>PPIU</option>
                <option value="PIHK" @selected($old('Status') === 'PIHK')>PIHK</option>
            </select>
            @error('Status')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="{{ $colClass }} {{ $fieldSpacing }}">
            <label for="Pimpinan" class="form-label">Nama Pimpinan / Direktur @include('partials.required-star')</label>
            <input type="text" class="{{ $inputClass }} @error('Pimpinan') is-invalid @enderror"
                id="Pimpinan" name="Pimpinan" value="{{ $old('Pimpinan') }}"
                placeholder="{{ $compact ? 'Nama direktur atau pemilik' : '' }}" required>
            @error('Pimpinan')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    @endif

    @if ($show('izin'))
        <div class="{{ $colClass }} {{ $fieldSpacing }}">
            <label for="Pusat" class="form-label">{{ $compact ? 'Nomor SK / NIB Pusat' : 'No. SK / NIB Pusat' }} @include('partials.required-star')</label>
            <input type="text" class="{{ $inputClass }} @error('Pusat') is-invalid @enderror"
                id="Pusat" name="Pusat" value="{{ $old('Pusat') }}"
                placeholder="{{ $compact ? 'Nomor surat keputusan atau NIB' : '' }}" required>
            @error('Pusat')<div class="invalid-feedback">{{ $message }}</div>@enderror
            @if ($compact)<div class="form-text">Nomor yang tertera pada SK izin operasional</div>@endif
        </div>

        <div class="{{ $colClass }} {{ $fieldSpacing }}">
            <label for="Tanggal" class="form-label">Tanggal SK @include('partials.required-star')</label>
            <input type="date" class="{{ $inputClass }} @error('Tanggal') is-invalid @enderror"
                id="Tanggal" name="Tanggal" value="{{ $old('Tanggal') }}" required>
            @error('Tanggal')<div class="invalid-feedback">{{ $message }}</div>@enderror
            @if ($compact)<div class="form-text">Tanggal terbit surat keputusan</div>@endif
        </div>
    @endif

    @if ($show('akreditasi'))
        <div class="{{ $colAkreditasi }} {{ $fieldSpacing }}">
            <label for="nilai_akreditasi" class="form-label">Nilai Akreditasi @include('partials.required-star')</label>
            <select class="{{ $selectClass }} @error('nilai_akreditasi') is-invalid @enderror"
                id="nilai_akreditasi" name="nilai_akreditasi" required>
                <option value="">Pilih nilai</option>
                @foreach (['A', 'B', 'C'] as $grade)
                    <option value="{{ $grade }}" @selected($old('nilai_akreditasi') === $grade)>Nilai {{ $grade }}</option>
                @endforeach
            </select>
            @error('nilai_akreditasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="{{ $colAkreditasi }} {{ $fieldSpacing }}">
            <label for="tanggal_akreditasi" class="form-label">Tanggal Akreditasi @include('partials.required-star')</label>
            <input type="date" class="{{ $inputClass }} @error('tanggal_akreditasi') is-invalid @enderror"
                id="tanggal_akreditasi" name="tanggal_akreditasi" value="{{ $old('tanggal_akreditasi') }}" required>
            @error('tanggal_akreditasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="{{ $colAkreditasi }} {{ $fieldSpacing }}">
            <label for="lembaga_akreditasi" class="form-label">Lembaga Akreditasi @include('partials.required-star')</label>
            <input type="text" class="{{ $inputClass }} @error('lembaga_akreditasi') is-invalid @enderror"
                id="lembaga_akreditasi" name="lembaga_akreditasi"
                value="{{ $old('lembaga_akreditasi', 'Kementerian Haji dan Umroh') }}" required>
            @error('lembaga_akreditasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    @endif

    @if ($show('alamat'))
        <div class="{{ $colClass }} {{ $fieldSpacing }}">
            <label for="Telepon" class="form-label">Telepon Kantor @include('partials.required-star')</label>
            <input type="tel" class="{{ $inputClass }} @error('Telepon') is-invalid @enderror"
                id="Telepon" name="Telepon" value="{{ $old('Telepon') }}"
                maxlength="16" inputmode="numeric"
                placeholder="{{ $compact ? 'Contoh: 081234567890' : '081234567890' }}" required>
            @error('Telepon')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="{{ $colClass }} {{ $fieldSpacing }}">
            <label for="kab_kota" class="form-label">Kabupaten / Kota @include('partials.required-star')</label>
            <select class="{{ $selectClass }} @error('kab_kota') is-invalid @enderror" id="kab_kota" name="kab_kota" required>
                <option value="">Pilih kabupaten/kota</option>
                @foreach ($kabupatens as $kab)
                    <option value="{{ $kab }}" @selected($old('kab_kota') === $kab)>{{ $kab }}</option>
                @endforeach
            </select>
            @error('kab_kota')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="{{ $colAlamatHalf }} {{ $fieldSpacing }}">
            <label for="alamat_kantor_lama" class="form-label">Alamat Kantor (Lama) @include('partials.required-star')</label>
            <textarea class="{{ $inputClass }} @error('alamat_kantor_lama') is-invalid @enderror"
                id="alamat_kantor_lama" name="alamat_kantor_lama" rows="{{ $compact ? 3 : 2 }}"
                placeholder="{{ $compact ? 'Alamat kantor sesuai izin lama' : '' }}" required>{{ $old('alamat_kantor_lama') }}</textarea>
            @error('alamat_kantor_lama')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="{{ $colAlamatFull }} {{ $fieldSpacing }}">
            <label for="alamat_kantor_baru" class="form-label">Alamat Kantor (Saat Ini) @include('partials.required-star')</label>
            <textarea class="{{ $inputClass }} @error('alamat_kantor_baru') is-invalid @enderror"
                id="alamat_kantor_baru" name="alamat_kantor_baru" rows="{{ $compact ? 3 : 2 }}"
                placeholder="{{ $compact ? 'Alamat kantor saat ini' : '' }}" required>{{ $old('alamat_kantor_baru') }}</textarea>
            @error('alamat_kantor_baru')<div class="invalid-feedback">{{ $message }}</div>@enderror
            @if ($compact)
                <div class="form-text">Isi sama dengan alamat lama jika belum pindah kantor</div>
            @else
                <small class="text-muted">Isi sama dengan alamat lama jika belum pindah</small>
            @endif
        </div>
    @endif
</div>
