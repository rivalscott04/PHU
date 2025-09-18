@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Jamaah</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('jamaah.update', $jamaah->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nik" class="form-label">NIK</label>
                                <input type="text" class="form-control @error('nik') is-invalid @enderror" id="nik"
                                    name="nik" value="{{ old('nik', $jamaah->nik) }}" readonly>
                                @error('nik')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="nama" class="form-label">Nama</label>
                                <input type="text" class="form-control @error('nama') is-invalid @enderror"
                                    id="nama" name="nama" value="{{ old('nama', $jamaah->nama) }}">
                                @error('nama')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="alamat" class="form-label">Alamat</label>
                                <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="3">{{ old('alamat', $jamaah->alamat) }}</textarea>
                                @error('alamat')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="nomor_hp" class="form-label">Nomor HP</label>
                                <input type="text" class="form-control @error('nomor_hp') is-invalid @enderror"
                                    id="nomor_hp" name="nomor_hp" value="{{ old('nomor_hp', $jamaah->nomor_hp) }}">
                                @error('nomor_hp')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('jamaah.detail', $jamaah->id) }}" class="btn btn-secondary">Batal</a>
                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
// Auto format phone number with validation
document.getElementById('nomor_hp').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    e.target.value = value;
    
    // Validate phone number starts with 08
    const phoneInput = e.target;
    const phoneValue = phoneInput.value;
    
    // Remove existing validation message
    const existingMessage = phoneInput.parentNode.querySelector('.phone-validation-message');
    if (existingMessage) {
        existingMessage.remove();
    }
    
    // Add validation if phone number is entered but doesn't start with 08
    if (phoneValue && phoneValue.length > 0 && !phoneValue.startsWith('08')) {
        const errorMessage = document.createElement('div');
        errorMessage.className = 'phone-validation-message text-danger small mt-1';
        errorMessage.textContent = 'Nomor HP harus diawali dengan 08';
        phoneInput.parentNode.appendChild(errorMessage);
        phoneInput.classList.add('is-invalid');
    } else if (phoneValue && phoneValue.startsWith('08')) {
        phoneInput.classList.remove('is-invalid');
        phoneInput.classList.add('is-valid');
    } else {
        phoneInput.classList.remove('is-invalid', 'is-valid');
    }
});
</script>
@endsection
