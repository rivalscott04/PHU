@extends('layouts.app')

@section('title', 'Buat Sertifikat PPIU')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Buat Sertifikat PPIU</h4>
                <div class="page-title-right">
                    <a href="{{ route('sertifikat.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                                <div class="card-body">
                    <!-- Form untuk Sertifikat Pusat -->
                    <form action="{{ route('sertifikat.store') }}" method="POST" id="form-pusat">
                        @csrf
                                                 <input type="hidden" name="jenis_lokasi" value="pusat">
                        
                                                                         <!-- Tab Navigation -->
                        <ul class="nav nav-tabs mb-4" id="sertifikatTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="pusat-tab" data-bs-toggle="tab" data-bs-target="#pusat" type="button" role="tab" aria-controls="pusat" aria-selected="true">
                                    <i class="bx bx-building"></i> Sertifikat Pusat
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="cabang-tab" data-bs-toggle="tab" data-bs-target="#cabang" type="button" role="tab" aria-controls="cabang" aria-selected="false">
                                    <i class="bx bx-map"></i> Sertifikat Cabang
                                </button>
                            </li>
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content" id="sertifikatTabContent">
                            <!-- Tab Pusat -->
                            <div class="tab-pane fade show active" id="pusat" role="tabpanel" aria-labelledby="pusat-tab">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="travel_id_pusat" class="form-label">Travel Company Pusat *</label>
                                            <select name="travel_id" id="travel_id_pusat" class="form-select @error('travel_id') is-invalid @enderror" required>
                                                <option value="">Pilih Travel Company Pusat</option>
                                                @foreach($travels as $travel)
                                                    <option value="{{ $travel->id }}" 
                                                            {{ old('travel_id') == $travel->id ? 'selected' : '' }}>
                                                        {{ $travel->Penyelenggara }}, {{ $travel->Status }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('travel_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">Data PPIU dan Kepala akan terisi otomatis</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Field lainnya untuk form pusat -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="nama_ppiu_pusat" class="form-label">Nama PPIU *</label>
                                            <input type="text" class="form-control @error('nama_ppiu') is-invalid @enderror" 
                                                   id="nama_ppiu_pusat" name="nama_ppiu" value="{{ old('nama_ppiu') }}" required readonly>
                                            @error('nama_ppiu')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">Akan terisi otomatis dari data travel</small>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="nama_kepala_pusat" class="form-label">Nama Kepala Cabang *</label>
                                            <input type="text" class="form-control @error('nama_kepala') is-invalid @enderror" 
                                                   id="nama_kepala_pusat" name="nama_kepala" value="{{ old('nama_kepala') }}" required readonly>
                                            @error('nama_kepala')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">Akan terisi otomatis dari data travel</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="alamat_pusat" class="form-label">Alamat Kantor *</label>
                                    <textarea class="form-control @error('alamat') is-invalid @enderror" 
                                              id="alamat_pusat" name="alamat" rows="3" required readonly>{{ old('alamat') }}</textarea>
                                    @error('alamat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Akan terisi otomatis dari data travel</small>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="tanggal_diterbitkan_pusat" class="form-label">Tanggal Diterbitkan *</label>
                                            <input type="date" class="form-control @error('tanggal_diterbitkan') is-invalid @enderror" 
                                                   id="tanggal_diterbitkan_pusat" name="tanggal_diterbitkan" value="{{ old('tanggal_diterbitkan') }}" required>
                                            @error('tanggal_diterbitkan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-light border">
                                    <i class="bx bx-info-circle text-primary me-1"></i>
                                    Nomor surat dan nomor dokumen diterbitkan sistem saat sertifikat disimpan,
                                    berurutan sepanjang tahun. Masa berlaku sertifikat berakhir 1 Januari berikutnya.
                                </div>
</div>



                   <script>
         document.addEventListener('DOMContentLoaded', function() {
             // Auto-fill form when travel pusat is selected
             document.getElementById('travel_id_pusat').addEventListener('change', function() {
                 const travelId = this.value;
                 if (travelId) {
                     // Fetch travel data via AJAX
                     fetch(`/sertifikat/travel-data/${travelId}`)
                         .then(response => response.json())
                         .then(data => {
                             document.getElementById('nama_ppiu_pusat').value = data.nama_ppiu;
                             document.getElementById('nama_kepala_pusat').value = data.nama_kepala;
                             document.getElementById('alamat_pusat').value = data.alamat;
                         })
                         .catch(error => {
                             console.error('Error fetching travel data:', error);
                         });
                 } else {
                     // Clear fields if no travel selected
                     document.getElementById('nama_ppiu_pusat').value = '';
                     document.getElementById('nama_kepala_pusat').value = '';
                     document.getElementById('alamat_pusat').value = '';
                 }
             });

                           // Auto-fill form when cabang is selected
              document.getElementById('cabang_id_cabang').addEventListener('change', function() {
                  const cabangId = this.value;
                  if (cabangId) {
                      // Fetch cabang data via AJAX
                      fetch(`/sertifikat/cabang-data/${cabangId}`)
                          .then(response => response.json())
                          .then(data => {
                              document.getElementById('nama_ppiu_cabang').value = data.nama_ppiu;
                              document.getElementById('nama_kepala_cabang').value = data.nama_kepala;
                              document.getElementById('alamat_cabang').value = data.alamat;
                          })
                          .catch(error => {
                              console.error('Error fetching cabang data:', error);
                          });
                  } else {
                      // Clear fields if no cabang selected
                      document.getElementById('nama_ppiu_cabang').value = '';
                      document.getElementById('nama_kepala_cabang').value = '';
                      document.getElementById('alamat_cabang').value = '';
                  }
              });

             // Handle tab switching
             const pusatTab = document.getElementById('pusat-tab');
             const cabangTab = document.getElementById('cabang-tab');
             const formPusat = document.getElementById('form-pusat');
             const formCabang = document.getElementById('form-cabang');

                           pusatTab.addEventListener('click', function() {
                  // Show form pusat, hide form cabang
                  formPusat.style.display = 'block';
                  formCabang.style.display = 'none';
                  
                  // Disable all fields in cabang form to prevent validation
                  const cabangFields = formCabang.querySelectorAll('input, select, textarea');
                  cabangFields.forEach(field => {
                      field.disabled = true;
                      field.removeAttribute('required');
                  });
                  
                  // Enable all fields in pusat form
                  const pusatFields = formPusat.querySelectorAll('input, select, textarea');
                  pusatFields.forEach(field => {
                      field.disabled = false;
                      if (field.hasAttribute('data-required')) {
                          field.setAttribute('required', 'required');
                      }
                  });
                  
                                     // Clear cabang form fields (not needed since form is separate)
              });

              cabangTab.addEventListener('click', function() {
                  // Show form cabang, hide form pusat
                  formCabang.style.display = 'block';
                  formPusat.style.display = 'none';
                  
                  // Disable all fields in pusat form to prevent validation
                  const pusatFields = formPusat.querySelectorAll('input, select, textarea');
                  pusatFields.forEach(field => {
                      field.disabled = true;
                      field.removeAttribute('required');
                  });
                  
                  // Enable all fields in cabang form
                  const cabangFields = formCabang.querySelectorAll('input, select, textarea');
                  cabangFields.forEach(field => {
                      field.disabled = false;
                      if (field.hasAttribute('data-required')) {
                          field.setAttribute('required', 'required');
                      }
                  });
                  
                                     // Clear pusat form fields (not needed since form is separate)
              });

                           // Initialize - show form pusat by default
              formPusat.style.display = 'block';
              formCabang.style.display = 'none';
              
              // Disable cabang form fields initially
              const cabangFields = formCabang.querySelectorAll('input, select, textarea');
              cabangFields.forEach(field => {
                  field.disabled = true;
                  field.removeAttribute('required');
              });
              
                             // Add data-required attribute to all required fields
               const allRequiredFields = document.querySelectorAll('[required]');
               allRequiredFields.forEach(field => {
                   field.setAttribute('data-required', 'true');
               });

               // Penomoran tidak lagi diisi dari sini. Nomor surat dan nomor
               // dokumen diterbitkan sistem saat sertifikat disimpan.
          });
         </script>
@endsection 