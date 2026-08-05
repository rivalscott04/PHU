@php
    use App\Enums\UserRole;

    $filters = $filters ?? [];
    $showKabupaten = in_array(auth()->user()->role, [UserRole::Admin->value, UserRole::Pimpinan->value], true);
@endphp

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            @if ($showKabupaten)
                <div class="col-md-3">
                    <label class="form-label mb-1">Kabupaten</label>
                    <select name="kabupaten" class="form-select form-select-sm">
                        <option value="">Semua Kabupaten</option>
                        @foreach ($filters['kabupaten_options'] ?? [] as $kab)
                            <option value="{{ $kab }}" @selected(request('kabupaten') === $kab)>{{ $kab }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="col-md-2">
                <label class="form-label mb-1">Jenis Travel</label>
                <select name="jenis_travel" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    <option value="PPIU" @selected(request('jenis_travel') === 'PPIU')>PPIU</option>
                    <option value="PIHK" @selected(request('jenis_travel') === 'PIHK')>PIHK</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label mb-1">Risiko</label>
                <select name="risk_level" class="form-select form-select-sm">
                    <option value="">Semua</option>
                    <option value="CRITICAL" @selected(request('risk_level') === 'CRITICAL')>Kritis</option>
                    <option value="HIGH" @selected(request('risk_level') === 'HIGH')>Tinggi</option>
                    <option value="MEDIUM" @selected(request('risk_level') === 'MEDIUM')>Sedang</option>
                    <option value="LOW" @selected(request('risk_level') === 'LOW')>Rendah</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label mb-1">Urutkan</label>
                <select name="sort" class="form-select form-select-sm">
                    <option value="">Nama travel</option>
                    <option value="risk" @selected(request('sort') === 'risk')>Risiko tertinggi</option>
                    <option value="pengaduan" @selected(request('sort') === 'pengaduan')>Pengaduan terbanyak</option>
                    <option value="bap_pending" @selected(request('sort') === 'bap_pending')>BA pending terbanyak</option>
                    <option value="inspection" @selected(request('sort') === 'inspection')>Terlama tidak diawasi</option>
                </select>
            </div>
            <div class="col-md-auto d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="bx bx-filter-alt me-1"></i> Terapkan
                </button>
                @if (request()->hasAny(['kabupaten', 'jenis_travel', 'risk_level', 'sort']))
                    <a href="{{ url()->current() }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>
