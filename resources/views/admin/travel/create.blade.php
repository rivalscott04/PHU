@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Tambah User Travel</h5>
                    <a href="{{ route('travels.index') }}" class="btn btn-secondary">
                        <i class="bx bx-arrow-back"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('travels.store') }}">
                        @csrf
                        <div class="row">
                            <!-- Field Wajib untuk Login -->
                            <div class="col-md-6 mb-3">
                                <label for="nama" class="form-label">Nama <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nama') is-invalid @enderror" 
                                       id="nama" name="nama" value="{{ old('nama') }}" required>
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nomor_hp" class="form-label">Nomor HP <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nomor_hp') is-invalid @enderror" 
                                       id="nomor_hp" name="nomor_hp" value="{{ old('nomor_hp') }}" required>
                                @error('nomor_hp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="travel_id" class="form-label">Travel Company <span class="text-danger">*</span></label>
                                <select class="form-control @error('travel_id') is-invalid @enderror" 
                                        id="travel_id" name="travel_id" required>
                                    <option value="">Pilih Travel Company</option>
                                    @foreach($travelCompanies as $travel)
                                        <option value="{{ $travel->id }}" {{ old('travel_id') == $travel->id ? 'selected' : '' }}>
                                            {{ $travel->Penyelenggara }} - {{ $travel->kab_kota }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('travel_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Informasi Tambahan -->
                        <div class="alert alert-info mt-3">
                            <h6 class="alert-heading"><i class="bx bx-info-circle"></i> Informasi Tambahan</h6>
                            <p class="mb-0">
                                Data profile lengkap (nama, alamat, dll) dapat diisi nanti oleh user melalui halaman profile setelah login.
                            </p>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save"></i> Simpan
                            </button>
                            <a href="{{ route('travels.index') }}" class="btn btn-secondary">
                                <i class="bx bx-x"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
