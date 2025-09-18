@extends('layouts.app')

@section('content')

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Tambah Data Jamaah</h5>
                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#uploadModal">Upload
                        XLSX</button>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('jamaah.haji.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nik" class="form-label">NIK</label>
                                <input type="text" class="form-control" id="nik" name="nik" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nama" class="form-label">Nama</label>
                                <input type="text" class="form-control" id="nama" name="nama" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="alamat" class="form-label">Alamat</label>
                                <textarea class="form-control" id="alamat" name="alamat" required></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nomor_hp" class="form-label">Nomor HP</label>
                                <input type="text" class="form-control" id="nomor_hp" name="nomor_hp" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal untuk Upload XLSX -->
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Upload Data Jamaah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('jamaah.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="file" class="form-label">Pilih File XLSX</label>
                            <input type="file" class="form-control" id="file" name="file" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Upload</button>
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

// Auto format NIK
document.getElementById('nik').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 16) {
        value = value.substring(0, 16);
    }
    e.target.value = value;
});
</script>
@endsection
