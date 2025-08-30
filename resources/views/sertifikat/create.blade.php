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
                         <input type="hidden" name="nomor_surat" value="{{ $nextNomorSurat }}">
                         <input type="hidden" name="nomor_dokumen" value="{{ $nextNomorDokumen }}">
                        
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
                                                        {{ $travel->Penyelenggara }} - {{ $travel->Status }}
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

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="nomor_surat_pusat" class="form-label">Nomor Surat *</label>
                                            <input type="number" class="form-control @error('nomor_surat') is-invalid @enderror" 
                                                   id="nomor_surat_pusat" name="nomor_surat" value="{{ $nextNomorSurat }}" 
                                                   placeholder="1, 2, 3, dst" readonly>
                                            <small class="form-text text-muted">Nomor urut surat (otomatis)</small>
                                            @error('nomor_surat')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="bulan_surat_pusat" class="form-label">Bulan Surat *</label>
                                            <input type="number" class="form-control @error('bulan_surat') is-invalid @enderror" 
                                                   id="bulan_surat_pusat" name="bulan_surat" value="{{ old('bulan_surat', date('m')) }}" 
                                                   min="1" max="12" required>
                                            <small class="form-text text-muted">Bulan (1-12)</small>
                                            @error('bulan_surat')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="tahun_surat_pusat" class="form-label">Tahun Surat *</label>
                                            <input type="number" class="form-control @error('tahun_surat') is-invalid @enderror" 
                                                   id="tahun_surat_pusat" name="tahun_surat" value="{{ old('tahun_surat', date('Y')) }}" 
                                                   min="2020" max="2030" required>
                                            <small class="form-text text-muted">Tahun (2020-2030)</small>
                                            @error('tahun_surat')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="nomor_dokumen_pusat" class="form-label">Nomor Dokumen *</label>
                                            <input type="text" class="form-control @error('nomor_dokumen') is-invalid @enderror" 
                                                   id="nomor_dokumen_pusat" name="nomor_dokumen" value="{{ $nextNomorDokumen }}" 
                                                   placeholder="001, 002, dst" readonly>
                                            <small class="form-text text-muted">3 digit angka (otomatis)</small>
                                            @error('nomor_dokumen')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="tanggal_tandatangan_pusat" class="form-label">Tanggal Tanda Tangan *</label>
                                            <input type="date" class="form-control @error('tanggal_tandatangan') is-invalid @enderror" 
                                                   id="tanggal_tandatangan_pusat" name="tanggal_tandatangan" value="{{ old('tanggal_tandatangan') }}" required>
                                            @error('tanggal_tandatangan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">Format dd-mm-yyyy akan di-generate otomatis</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Simpan Sertifikat Pusat
                                    </button>
                                </div>
                            </div>

                                                         <!-- Tab Cabang -->
                             <div class="tab-pane fade" id="cabang" role="tabpanel" aria-labelledby="cabang-tab">
                                 <div class="alert alert-info">
                                     <i class="fas fa-info-circle"></i> Silakan pilih tab "Sertifikat Cabang" untuk membuat sertifikat cabang.
                                 </div>

                                
                            </div>
                                                 </div>
                     </form>

                     <!-- Form untuk Sertifikat Cabang -->
                     <form action="{{ route('sertifikat.store') }}" method="POST" id="form-cabang" style="display: none;">
                         @csrf
                         <input type="hidden" name="jenis_lokasi" value="cabang">
                         <input type="hidden" name="nomor_surat" value="{{ $nextNomorSurat }}">
                         <input type="hidden" name="nomor_dokumen" value="{{ $nextNomorDokumen }}">
                         
                         <!-- Tab Navigation untuk Cabang -->
                         <ul class="nav nav-tabs mb-4" id="sertifikatTabCabang" role="tablist">
                             <li class="nav-item" role="presentation">
                                 <button class="nav-link" id="pusat-tab-cabang" data-bs-toggle="tab" data-bs-target="#pusat-cabang" type="button" role="tab" aria-controls="pusat-cabang" aria-selected="false">
                                     <i class="bx bx-building"></i> Sertifikat Pusat
                                 </button>
                             </li>
                             <li class="nav-item" role="presentation">
                                 <button class="nav-link active" id="cabang-tab-cabang" data-bs-toggle="tab" data-bs-target="#cabang-cabang" type="button" role="tab" aria-controls="cabang-cabang" aria-selected="true">
                                     <i class="bx bx-map"></i> Sertifikat Cabang
                                 </button>
                             </li>
                         </ul>

                         <!-- Tab Content untuk Cabang -->
                         <div class="tab-content" id="sertifikatTabContentCabang">
                             <!-- Tab Pusat (disabled) -->
                             <div class="tab-pane fade" id="pusat-cabang" role="tabpanel" aria-labelledby="pusat-tab-cabang">
                                 <div class="alert alert-info">
                                     <i class="fas fa-info-circle"></i> Silakan pilih tab "Sertifikat Cabang" untuk membuat sertifikat cabang.
                                 </div>
                             </div>

                                                           <!-- Tab Cabang -->
                              <div class="tab-pane fade show active" id="cabang-cabang" role="tabpanel" aria-labelledby="cabang-tab-cabang">
                                  <div class="row">
                                      <div class="col-md-12">
                                          <div class="mb-3">
                                              <label for="cabang_id_cabang" class="form-label">Travel Company Cabang *</label>
                                              <select name="cabang_id" id="cabang_id_cabang" class="form-select @error('cabang_id') is-invalid @enderror" required>
                                                  <option value="">Pilih Travel Company Cabang</option>
                                                  @foreach($cabangs as $cabang)
                                                      <option value="{{ $cabang->id_cabang }}" 
                                                              {{ old('cabang_id') == $cabang->id_cabang ? 'selected' : '' }}>
                                                          {{ $cabang->Penyelenggara }} - {{ $cabang->kabupaten }}
                                                      </option>
                                                  @endforeach
                                              </select>
                                              @error('cabang_id')
                                                  <div class="invalid-feedback">{{ $message }}</div>
                                              @enderror
                                              <small class="form-text text-muted">Data PPIU dan Kepala akan terisi otomatis</small>
                                          </div>
                                      </div>
                                  </div>

                                  <!-- Field lainnya untuk form cabang -->
                                  <div class="row">
                                      <div class="col-md-6">
                                          <div class="mb-3">
                                              <label for="nama_ppiu_cabang" class="form-label">Nama PPIU *</label>
                                              <input type="text" class="form-control @error('nama_ppiu') is-invalid @enderror" 
                                                     id="nama_ppiu_cabang" name="nama_ppiu" value="{{ old('nama_ppiu') }}" required readonly>
                                              @error('nama_ppiu')
                                                  <div class="invalid-feedback">{{ $message }}</div>
                                              @enderror
                                              <small class="form-text text-muted">Akan terisi otomatis dari data travel</small>
                                          </div>
                                      </div>
                                      
                                      <div class="col-md-6">
                                          <div class="mb-3">
                                              <label for="nama_kepala_cabang" class="form-label">Nama Kepala Cabang *</label>
                                              <input type="text" class="form-control @error('nama_kepala') is-invalid @enderror" 
                                                     id="nama_kepala_cabang" name="nama_kepala" value="{{ old('nama_kepala') }}" required readonly>
                                              @error('nama_kepala')
                                                  <div class="invalid-feedback">{{ $message }}</div>
                                              @enderror
                                              <small class="form-text text-muted">Akan terisi otomatis dari data travel</small>
                                          </div>
                                      </div>
                                  </div>

                                  <div class="mb-3">
                                      <label for="alamat_cabang" class="form-label">Alamat Kantor *</label>
                                      <textarea class="form-control @error('alamat') is-invalid @enderror" 
                                                id="alamat_cabang" name="alamat" rows="3" required readonly>{{ old('alamat') }}</textarea>
                                      @error('alamat')
                                          <div class="invalid-feedback">{{ $message }}</div>
                                      @enderror
                                      <small class="form-text text-muted">Akan terisi otomatis dari data travel</small>
                                  </div>

                                  <div class="row">
                                      <div class="col-md-6">
                                          <div class="mb-3">
                                              <label for="tanggal_diterbitkan_cabang" class="form-label">Tanggal Diterbitkan *</label>
                                              <input type="date" class="form-control @error('tanggal_diterbitkan') is-invalid @enderror" 
                                                     id="tanggal_diterbitkan_cabang" name="tanggal_diterbitkan" value="{{ old('tanggal_diterbitkan') }}" required>
                                              @error('tanggal_diterbitkan')
                                                  <div class="invalid-feedback">{{ $message }}</div>
                                              @enderror
                                          </div>
                                      </div>
                                  </div>

                                  <div class="row">
                                      <div class="col-md-3">
                                          <div class="mb-3">
                                              <label for="nomor_surat_cabang" class="form-label">Nomor Surat *</label>
                                              <input type="number" class="form-control @error('nomor_surat') is-invalid @enderror" 
                                                     id="nomor_surat_cabang" name="nomor_surat" value="{{ $nextNomorSurat }}" 
                                                     placeholder="1, 2, 3, dst" readonly>
                                              <small class="form-text text-muted">Nomor urut surat (otomatis)</small>
                                              @error('nomor_surat')
                                                  <div class="invalid-feedback">{{ $message }}</div>
                                              @enderror
                                          </div>
                                      </div>
                                      
                                      <div class="col-md-3">
                                          <div class="mb-3">
                                              <label for="bulan_surat_cabang" class="form-label">Bulan Surat *</label>
                                              <input type="number" class="form-control @error('bulan_surat') is-invalid @enderror" 
                                                     id="bulan_surat_cabang" name="bulan_surat" value="{{ old('bulan_surat', date('m')) }}" 
                                                     min="1" max="12" required>
                                              <small class="form-text text-muted">Bulan (1-12)</small>
                                              @error('bulan_surat')
                                                  <div class="invalid-feedback">{{ $message }}</div>
                                              @enderror
                                          </div>
                                      </div>
                                      
                                      <div class="col-md-3">
                                          <div class="mb-3">
                                              <label for="tahun_surat_cabang" class="form-label">Tahun Surat *</label>
                                              <input type="number" class="form-control @error('tahun_surat') is-invalid @enderror" 
                                                     id="tahun_surat_cabang" name="tahun_surat" value="{{ old('tahun_surat', date('Y')) }}" 
                                                     min="2020" max="2030" required>
                                              <small class="form-text text-muted">Tahun (2020-2030)</small>
                                              @error('tahun_surat')
                                                  <div class="invalid-feedback">{{ $message }}</div>
                                              @enderror
                                          </div>
                                      </div>
                                      
                                      <div class="col-md-3">
                                          <div class="mb-3">
                                              <label for="nomor_dokumen_cabang" class="form-label">Nomor Dokumen *</label>
                                              <input type="text" class="form-control @error('nomor_dokumen') is-invalid @enderror" 
                                                     id="nomor_dokumen_cabang" name="nomor_dokumen" value="{{ $nextNomorDokumen }}" 
                                                     placeholder="001, 002, dst" readonly>
                                              <small class="form-text text-muted">3 digit angka (otomatis)</small>
                                              @error('nomor_dokumen')
                                                  <div class="invalid-feedback">{{ $message }}</div>
                                              @enderror
                                          </div>
                                      </div>
                                  </div>

                                  <div class="row">
                                      <div class="col-md-6">
                                          <div class="mb-3">
                                              <label for="tanggal_tandatangan_cabang" class="form-label">Tanggal Tanda Tangan *</label>
                                              <input type="date" class="form-control @error('tanggal_tandatangan') is-invalid @enderror" 
                                                     id="tanggal_tandatangan_cabang" name="tanggal_tandatangan" value="{{ old('tanggal_tandatangan') }}" required>
                                              @error('tanggal_tandatangan')
                                                  <div class="invalid-feedback">{{ $message }}</div>
                                              @enderror
                                              <small class="form-text text-muted">Format dd-mm-yyyy akan di-generate otomatis</small>
                                          </div>
                                      </div>
                                  </div>

                                  <div class="d-flex justify-content-end">
                                      <button type="submit" class="btn btn-primary">
                                          <i class="fas fa-save"></i> Simpan Sertifikat Cabang
                                      </button>
                                  </div>
                              </div>
                         </div>
                     </form>
                </div>
            </div>
        </div>
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

               // Function to update nomor otomatis when bulan/tahun changes
               function updateNomorOtomatis() {
                   const bulanPusat = document.getElementById('bulan_surat_pusat').value;
                   const tahunPusat = document.getElementById('tahun_surat_pusat').value;
                   const bulanCabang = document.getElementById('bulan_surat_cabang').value;
                   const tahunCabang = document.getElementById('tahun_surat_cabang').value;

                   if (bulanPusat && tahunPusat) {
                       fetch(`/sertifikat/get-next-nomor?bulan=${bulanPusat}&tahun=${tahunPusat}`)
                           .then(response => response.json())
                           .then(data => {
                               document.getElementById('nomor_surat_pusat').value = data.nomor_surat;
                               document.getElementById('nomor_dokumen_pusat').value = data.nomor_dokumen;
                               // Update hidden inputs
                               document.querySelector('input[name="nomor_surat"]').value = data.nomor_surat;
                               document.querySelector('input[name="nomor_dokumen"]').value = data.nomor_dokumen;
                           });
                   }

                   if (bulanCabang && tahunCabang) {
                       fetch(`/sertifikat/get-next-nomor?bulan=${bulanCabang}&tahun=${tahunCabang}`)
                           .then(response => response.json())
                           .then(data => {
                               document.getElementById('nomor_surat_cabang').value = data.nomor_surat;
                               document.getElementById('nomor_dokumen_cabang').value = data.nomor_dokumen;
                               // Update hidden inputs for cabang form
                               const cabangForm = document.getElementById('form-cabang');
                               cabangForm.querySelector('input[name="nomor_surat"]').value = data.nomor_surat;
                               cabangForm.querySelector('input[name="nomor_dokumen"]').value = data.nomor_dokumen;
                           });
                   }
               }

               // Add event listeners for bulan and tahun changes
               document.getElementById('bulan_surat_pusat').addEventListener('change', updateNomorOtomatis);
               document.getElementById('tahun_surat_pusat').addEventListener('change', updateNomorOtomatis);
               document.getElementById('bulan_surat_cabang').addEventListener('change', updateNomorOtomatis);
               document.getElementById('tahun_surat_cabang').addEventListener('change', updateNomorOtomatis);
          });
         </script>
@endsection 