@php
    $selectedScope = old('pengawas_scope', $pengawasScope ?? \App\Enums\PengawasScopeMode::Single->value);
    $selectedKabupatens = old('pengawas_kabupatens', $pengawasKabupatens ?? []);
@endphp

<div id="pengawasScopeFields" class="border rounded p-3 mb-3 bg-light" @if(($showPengawasScope ?? false) || old('role') === 'pengawas' || ($isPengawas ?? false)) style="" @else style="display:none;" @endif>
    <h6 class="mb-3">Wilayah Akses Pengawas</h6>

    <div class="mb-3">
        <label class="form-label d-block">Mode akses @include('partials.required-star')</label>
        @foreach($pengawasScopeModes as $mode)
            <div class="form-check">
                <input class="form-check-input pengawas-scope-mode"
                       type="radio"
                       name="pengawas_scope"
                       id="pengawas_scope_{{ $mode->value }}"
                       value="{{ $mode->value }}"
                       @checked($selectedScope === $mode->value)>
                <label class="form-check-label" for="pengawas_scope_{{ $mode->value }}">
                    <strong>{{ $mode->label() }}</strong>
                    <span class="text-muted d-block small">{{ $mode->description() }}</span>
                </label>
            </div>
        @endforeach
        @error('pengawas_scope')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>

    <div id="pengawasSingleField" class="mb-0">
        <label for="pengawas_kabupaten_single" class="form-label">Kabupaten/Kota @include('partials.required-star')</label>
        <select id="pengawas_kabupaten_single" name="kabupaten" class="form-select @error('kabupaten') is-invalid @enderror">
            <option value="">Pilih kabupaten/kota</option>
            @foreach($kabupatens as $kabupatenOption)
                <option value="{{ $kabupatenOption }}" @selected(old('kabupaten', $singleKabupaten ?? '') === $kabupatenOption)>
                    {{ $kabupatenOption }}
                </option>
            @endforeach
        </select>
        @error('kabupaten')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>

    <div id="pengawasCustomField" class="mb-0" style="display:none;">
        <label class="form-label">Pilih kabupaten/kota yang diizinkan @include('partials.required-star')</label>
        <div class="row">
            @foreach($kabupatens as $kabupatenOption)
                <div class="col-md-6">
                    <div class="form-check">
                        <input class="form-check-input"
                               type="checkbox"
                               name="pengawas_kabupatens[]"
                               id="pengawas_kab_{{ $loop->index }}"
                               value="{{ $kabupatenOption }}"
                               @checked(in_array($kabupatenOption, $selectedKabupatens, true))>
                        <label class="form-check-label" for="pengawas_kab_{{ $loop->index }}">{{ $kabupatenOption }}</label>
                    </div>
                </div>
            @endforeach
        </div>
        @error('pengawas_kabupatens')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        @error('pengawas_kabupatens.*')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
    </div>

    <div id="pengawasAllInfo" class="alert alert-info mb-0 py-2 small" style="display:none;">
        Akses ke semua kabupaten/kota NTB.
    </div>
</div>

@once
    @push('js')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const scopeFields = document.getElementById('pengawasScopeFields');
                if (!scopeFields) return;

                const singleField = document.getElementById('pengawasSingleField');
                const customField = document.getElementById('pengawasCustomField');
                const allInfo = document.getElementById('pengawasAllInfo');
                const singleSelect = document.getElementById('pengawas_kabupaten_single');

                function syncPengawasScopeMode() {
                    const mode = document.querySelector('.pengawas-scope-mode:checked')?.value || 'single';
                    const scopeFieldsVisible = scopeFields.style.display !== 'none';
                    singleField.style.display = mode === 'single' ? 'block' : 'none';
                    customField.style.display = mode === 'custom' ? 'block' : 'none';
                    allInfo.style.display = mode === 'all' ? 'block' : 'none';
                    if (singleSelect) {
                        singleSelect.required = scopeFieldsVisible && mode === 'single';
                    }

                    const firstRadio = document.querySelector('.pengawas-scope-mode');
                    if (firstRadio) {
                        firstRadio.required = scopeFieldsVisible;
                    }
                }

                document.querySelectorAll('.pengawas-scope-mode').forEach((input) => {
                    input.addEventListener('change', syncPengawasScopeMode);
                });

                syncPengawasScopeMode();
            });
        </script>
    @endpush
@endonce
