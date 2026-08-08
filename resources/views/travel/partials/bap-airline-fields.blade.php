@php
    $airlineOptions = $airlineOptions ?? [];
    $resolveSelect = function (string $field) use ($value, $airlineOptions) {
        $current = trim((string) $value($field));
        $matched = collect($airlineOptions)->first(
            fn (string $name) => \App\Models\BapAirline::matchesName($current, $name)
        );

        if ($matched) {
            return ['select' => $matched, 'other' => '', 'is_other' => false];
        }

        if ($current !== '') {
            return ['select' => '__other__', 'other' => $current, 'is_other' => true];
        }

        return ['select' => '', 'other' => '', 'is_other' => false];
    };

    $departure = $resolveSelect('airlines');
    $return = $resolveSelect('airlines2');
    $departureSelect = old('airlines_select', $departure['select']);
    $departureOther = old('airlines_other', $departure['other']);
    $returnSelect = old('airlines2_select', $return['select']);
    $returnOther = old('airlines2_other', $return['other']);
    $sameReturn = old('same_return_airline', ($departure['select'] !== '' && $departure['select'] === $return['select'] && ! $departure['is_other']) ? '1' : '0');
    if ($departure['is_other'] && $return['is_other'] && $departure['other'] === $return['other'] && $departure['other'] !== '') {
        $sameReturn = old('same_return_airline', '1');
    }
@endphp

<div class="col-12 mb-3">
    <div class="border rounded p-3 bg-light">
        <h6 class="text-uppercase text-muted small fw-semibold mb-3">Keberangkatan</h6>
        <div class="row g-3">
            <div class="col-md-6">
                <label for="datetime" class="form-label">Tanggal Berangkat <span class="text-danger">*</span></label>
                <input type="date" class="form-control @error('datetime') is-invalid @enderror" id="datetime" name="datetime"
                    value="{{ $value('datetime') ? \Carbon\Carbon::parse($value('datetime'))->format('Y-m-d') : '' }}" required>
                @error('datetime')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label for="airlines_select" class="form-label">Maskapai Keberangkatan <span class="text-danger">*</span></label>
                <select class="form-control @error('airlines') is-invalid @enderror" id="airlines_select" name="airlines_select" required>
                    <option value="">Pilih maskapai</option>
                    @foreach ($airlineOptions as $airlineName)
                        <option value="{{ $airlineName }}" @selected($departureSelect === $airlineName)>{{ $airlineName }}</option>
                    @endforeach
                    <option value="__other__" @selected($departureSelect === '__other__')>Lainnya (ketik manual)</option>
                </select>
                @error('airlines')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                <input type="text" class="form-control mt-2 @error('airlines_other') is-invalid @enderror" id="airlines_other"
                    name="airlines_other" value="{{ $departureOther }}" placeholder="Nama maskapai lainnya"
                    @if ($departureSelect !== '__other__') hidden @endif>
                @error('airlines_other')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                <small class="form-text text-muted">Pilih sesuai tiket pesawat jamaah. Daftar disediakan Kanwil NTB.</small>
            </div>
        </div>
    </div>
</div>

<div class="col-12 mb-3">
    <div class="border rounded p-3">
        <h6 class="text-uppercase text-muted small fw-semibold mb-3">Kepulangan</h6>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="same_return_airline" name="same_return_airline" value="1"
                @checked($sameReturn === '1')>
            <label class="form-check-label" for="same_return_airline">
                Maskapai pulang sama dengan keberangkatan
            </label>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label for="returndate" class="form-label">Tanggal Kepulangan</label>
                <input type="date" class="form-control" id="returndate" name="returndate" readonly
                    value="{{ $value('returndate') ? \Carbon\Carbon::parse($value('returndate'))->format('Y-m-d') : '' }}">
                <small class="form-text text-muted">Dihitung otomatis dari tanggal berangkat dan jumlah hari.</small>
            </div>
            <div class="col-md-6" id="returnAirlineGroup">
                <label for="airlines2_select" class="form-label">Maskapai Kepulangan <span class="text-danger">*</span></label>
                <select class="form-control @error('airlines2') is-invalid @enderror" id="airlines2_select" name="airlines2_select" required>
                    <option value="">Pilih maskapai</option>
                    @foreach ($airlineOptions as $airlineName)
                        <option value="{{ $airlineName }}" @selected($returnSelect === $airlineName)>{{ $airlineName }}</option>
                    @endforeach
                    <option value="__other__" @selected($returnSelect === '__other__')>Lainnya (ketik manual)</option>
                </select>
                @error('airlines2')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                <input type="text" class="form-control mt-2 @error('airlines2_other') is-invalid @enderror" id="airlines2_other"
                    name="airlines2_other" value="{{ $returnOther }}" placeholder="Nama maskapai lainnya"
                    @if ($returnSelect !== '__other__') hidden @endif>
                @error('airlines2_other')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
</div>
