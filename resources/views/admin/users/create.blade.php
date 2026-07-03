@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Tambah Pengguna</h5>
                    <a href="{{ route('users.index', ['tab' => request('role', 'kabupaten')]) }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3"><span class="text-danger">*</span> Wajib diisi</p>

                    <form method="POST" action="{{ route('users.store') }}" id="createUserForm">
                        @csrf

                        <div class="mb-3">
                            <label for="role" class="form-label">Role @include('partials.required-star')</label>
                            <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
                                <option value="">Pilih role</option>
                                @foreach($roleOptions as $roleOption)
                                    <option value="{{ $roleOption->value }}" @selected(old('role', request('role')) === $roleOption->value)>
                                        {{ $roleOption->label() }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nama" class="form-label">Nama @include('partials.required-star')</label>
                                <input type="text" id="nama" name="nama" class="form-control @error('nama') is-invalid @enderror"
                                       value="{{ old('nama') }}" required>
                                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email @include('partials.required-star')</label>
                                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nomor_hp" class="form-label">Nomor HP @include('partials.required-star')</label>
                                <input type="text" id="nomor_hp" name="nomor_hp" class="form-control @error('nomor_hp') is-invalid @enderror"
                                       value="{{ old('nomor_hp') }}" placeholder="08xxxxxxxxxx" required>
                                @error('nomor_hp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password Awal @include('partials.required-star')</label>
                                <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div id="kabupatenField" class="mb-3" style="display:none;">
                            <label for="kabupaten" class="form-label">Kabupaten/Kota Wilayah Kerja @include('partials.required-star')</label>
                            <select id="kabupaten" name="kabupaten" class="form-select @error('kabupaten') is-invalid @enderror">
                                <option value="">Pilih kabupaten/kota</option>
                                @foreach($kabupatens as $kabupaten)
                                    <option value="{{ $kabupaten }}" @selected(old('kabupaten') === $kabupaten)>{{ $kabupaten }}</option>
                                @endforeach
                            </select>
                            @error('kabupaten')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            <div class="form-text">Admin kabupaten hanya dapat mengakses data di wilayah ini.</div>
                        </div>

                        @include('admin.users.partials.pengawas-scope-fields')

                        <div id="travelField" class="mb-3" style="display:none;">
                            <label for="travel_id" class="form-label">PPIU / Travel @include('partials.required-star')</label>
                            <select id="travel_id" name="travel_id" class="form-select @error('travel_id') is-invalid @enderror">
                                <option value="">Pilih travel company</option>
                                @foreach($travelCompanies as $travel)
                                    <option value="{{ $travel->id }}" @selected(old('travel_id') == $travel->id)>
                                        {{ $travel->Penyelenggara }}, {{ $travel->kab_kota }}
                                    </option>
                                @endforeach
                            </select>
                            @error('travel_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save"></i> Simpan Pengguna
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const roleSelect = document.getElementById('role');
    const kabupatenField = document.getElementById('kabupatenField');
    const pengawasScopeFields = document.getElementById('pengawasScopeFields');
    const travelField = document.getElementById('travelField');
    const kabupatenInput = document.getElementById('kabupaten');
    const travelInput = document.getElementById('travel_id');

    function syncRoleFields() {
        const role = roleSelect.value;
        const isKabupaten = role === 'kabupaten';
        const isPengawas = role === 'pengawas';

        kabupatenField.style.display = isKabupaten ? 'block' : 'none';
        pengawasScopeFields.style.display = isPengawas ? 'block' : 'none';
        travelField.style.display = role === 'user' ? 'block' : 'none';
        kabupatenInput.required = isKabupaten;
        travelInput.required = role === 'user';

        const firstPengawasRadio = document.querySelector('.pengawas-scope-mode');
        if (firstPengawasRadio) {
            firstPengawasRadio.required = isPengawas;
        }
    }

    roleSelect.addEventListener('change', syncRoleFields);
    syncRoleFields();

    document.getElementById('createUserForm').addEventListener('submit', function (event) {
        if (roleSelect.value !== 'pengawas') return;
        if (document.querySelector('.pengawas-scope-mode:checked')?.value !== 'custom') return;
        if (document.querySelectorAll('input[name="pengawas_kabupatens[]"]:checked').length > 0) return;

        event.preventDefault();
        alert('Pilih minimal satu kabupaten/kota untuk mode akses kustom.');
    });
});
</script>
@endpush
