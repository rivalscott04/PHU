@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit User {{ $user->role === 'kabupaten' ? 'Kabupaten' : 'Travel' }}</h5>
                    <a href="{{ $user->role === 'kabupaten' ? route('kabupaten.index') : route('travels.index') }}" class="btn btn-secondary">
                        <i class="bx bx-arrow-back"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('users.update', $user->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nama" class="form-label">Nama</label>
                                <input type="text" class="form-control @error('nama') is-invalid @enderror" 
                                       id="nama" name="nama" value="{{ old('nama', $user->nama) }}" required>
                                @error('nama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nomor_hp" class="form-label">Nomor HP</label>
                                <input type="text" class="form-control @error('nomor_hp') is-invalid @enderror" 
                                       id="nomor_hp" name="nomor_hp" value="{{ old('nomor_hp', $user->nomor_hp) }}" required>
                                @error('nomor_hp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            @if($user->role === 'kabupaten')
                                <div class="col-md-6 mb-3">
                                    <label for="kabupaten" class="form-label">Kabupaten/Kota</label>
                                    <input type="text" class="form-control @error('kabupaten') is-invalid @enderror" 
                                           id="kabupaten" name="kabupaten" value="{{ old('kabupaten', $user->kabupaten) }}" 
                                           placeholder="Contoh: Lombok Barat, Bima, Sumbawa" required>
                                    @error('kabupaten')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password Baru (kosongkan jika tidak ingin mengubah)</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            @if($user->role === 'user')
                                <div class="col-md-6 mb-3">
                                    <label for="travel_id" class="form-label">Travel Company</label>
                                    <select class="form-control @error('travel_id') is-invalid @enderror" 
                                            id="travel_id" name="travel_id" required>
                                        <option value="">Pilih Travel Company</option>
                                        @foreach($travelCompanies as $travel)
                                            <option value="{{ $travel->id }}" {{ old('travel_id', $user->travel_id) == $travel->id ? 'selected' : '' }}>
                                                {{ $travel->Penyelenggara }} - {{ $travel->kab_kota }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('travel_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif
                            @if($user->role !== 'kabupaten')
                                <div class="col-md-6 mb-3">
                                    <label for="city" class="form-label">Kota</label>
                                    <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                           id="city" name="city" value="{{ old('city', $user->city) }}" required>
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif
                            <div class="col-md-6 mb-3">
                                <label for="country" class="form-label">Negara</label>
                                <input type="text" class="form-control @error('country') is-invalid @enderror" 
                                       id="country" name="country" value="{{ old('country', $user->country) }}" required>
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="postal" class="form-label">Kode Pos</label>
                                <input type="text" class="form-control @error('postal') is-invalid @enderror" 
                                       id="postal" name="postal" value="{{ old('postal', $user->postal) }}" required>
                                @error('postal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label for="address" class="form-label">Alamat</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" 
                                          id="address" name="address" rows="3" required>{{ old('address', $user->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save"></i> Update
                            </button>
                            <a href="{{ $user->role === 'kabupaten' ? route('kabupaten.index') : route('travels.index') }}" class="btn btn-secondary">
                                <i class="bx bx-x"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
