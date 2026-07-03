@extends('layouts.app')

@php
    $role = \App\Enums\UserRole::tryFromString($user->role);
    $isKabupaten = $user->role === 'kabupaten';
    $isPengawas = $user->role === 'pengawas';
@endphp

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">Edit Pengguna</h5>
                        <span class="badge bg-primary-subtle text-primary">{{ $role?->label() ?? $user->role }}</span>
                    </div>
                    <a href="{{ route('users.index', ['tab' => $user->role]) }}" class="btn btn-secondary">
                        <i class="bx bx-arrow-back"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3"><span class="text-danger">*</span> Wajib diisi</p>
                    <form method="POST" action="{{ route('users.update', $user->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nama" class="form-label">Nama @include('partials.required-star')</label>
                                <input type="text" class="form-control @error('nama') is-invalid @enderror"
                                       id="nama" name="nama" value="{{ old('nama', $user->nama) }}" required>
                                @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email @include('partials.required-star')</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nomor_hp" class="form-label">Nomor HP @include('partials.required-star')</label>
                                <input type="text" class="form-control @error('nomor_hp') is-invalid @enderror"
                                       id="nomor_hp" name="nomor_hp" value="{{ old('nomor_hp', $user->nomor_hp) }}" required>
                                @error('nomor_hp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            @if($isKabupaten)
                                <div class="col-md-6 mb-3">
                                    <label for="kabupaten" class="form-label">Kabupaten/Kota Wilayah Kerja @include('partials.required-star')</label>
                                    <select id="kabupaten" name="kabupaten" class="form-select @error('kabupaten') is-invalid @enderror" required>
                                        <option value="">Pilih kabupaten/kota</option>
                                        @foreach($kabupatens as $kabupatenOption)
                                            <option value="{{ $kabupatenOption }}" @selected(old('kabupaten', $user->kabupaten) === $kabupatenOption)>
                                                {{ $kabupatenOption }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('kabupaten')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            @endif
                            @if($isPengawas)
                                <div class="col-12 mb-3">
                                    @include('admin.users.partials.pengawas-scope-fields')
                                </div>
                            @endif
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password Baru (kosongkan jika tidak ingin mengubah)</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       id="password" name="password">
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            @if($user->role === 'user')
                                <div class="col-md-6 mb-3">
                                    <label for="travel_id" class="form-label">Travel Company @include('partials.required-star')</label>
                                    <select class="form-select @error('travel_id') is-invalid @enderror"
                                            id="travel_id" name="travel_id" required>
                                        <option value="">Pilih Travel Company</option>
                                        @foreach($travelCompanies as $travel)
                                            <option value="{{ $travel->id }}" @selected(old('travel_id', $user->travel_id) == $travel->id)>
                                                {{ $travel->Penyelenggara }}, {{ $travel->kab_kota }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('travel_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            @endif
                            @if(! $isKabupaten && ! $isPengawas)
                                <div class="col-md-6 mb-3">
                                    <label for="city" class="form-label">Kota</label>
                                    <input type="text" class="form-control @error('city') is-invalid @enderror"
                                           id="city" name="city" value="{{ old('city', $user->city) }}">
                                    @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            @endif
                            <div class="col-md-6 mb-3">
                                <label for="country" class="form-label">Negara</label>
                                <input type="text" class="form-control @error('country') is-invalid @enderror"
                                       id="country" name="country" value="{{ old('country', $user->country) }}">
                                @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save"></i> Update
                            </button>
                            <a href="{{ route('users.index', ['tab' => $user->role]) }}" class="btn btn-secondary">
                                <i class="bx bx-x"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
