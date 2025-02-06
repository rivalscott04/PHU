@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Data</h5>
                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#uploadModal">Upload
                        XLSX</button>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('travel.update', $travelCompany->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <!-- Penyelenggara -->
                            <div class="col-md-6 mb-3">
                                <label for="Penyelenggara" class="form-label">Penyelenggara</label>
                                <input type="text" class="form-control" id="Penyelenggara" name="Penyelenggara"
                                    value="{{ $travelCompany->Penyelenggara }}" required>
                            </div>

                            <!-- Status -->
                            <div class="col-md-6 mb-3">
                                <label for="Status" class="form-label">Status</label>
                                <select class="form-control" id="Status" name="Status" required>
                                    <option value="">Pilih Status</option>
                                    <option value="PPIU" {{ $travelCompany->Status == 'PPIU' ? 'selected' : '' }}>PPIU
                                    </option>
                                    <option value="PIHK" {{ $travelCompany->Status == 'PIHK' ? 'selected' : '' }}>PIHK
                                    </option>
                                </select>
                            </div>
                            <!-- Pusat -->
                            <div class="col-md-6 mb-3">
                                <label for="Pusat" class="form-label">No SK/NIB Pusat</label>
                                <input type="text" class="form-control" id="Pusat" name="Pusat"
                                    value="{{ $travelCompany->Pusat }}" required>
                            </div>
                            <!-- Tanggal -->
                            <div class="col-md-6 mb-3">
                                <label for="Tanggal" class="form-label">Tanggal</label>
                                <input type="date" class="form-control" id="Tanggal" name="Tanggal"
                                    value="{{ $travelCompany->Tanggal }}">
                            </div>
                            <!-- Nilai Akreditasi -->
                            <div class="col-md-6 mb-3">
                                <label for="nilai_akreditasi" class="form-label">Nilai Akreditasi</label>
                                <input type="text" class="form-control" id="nilai_akreditasi" name="nilai_akreditasi"
                                    value="{{ $travelCompany->nilai_akreditasi }}" required>
                            </div>
                            <!-- Tanggal Akreditasi -->
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_akreditasi" class="form-label">Tanggal Akreditasi</label>
                                <input type="date" class="form-control" id="tanggal_akreditasi" name="tanggal_akreditasi"
                                    value="{{ $travelCompany->tanggal_akreditasi }}">
                            </div>
                            <!-- Lembaga Akreditasi -->
                            <div class="col-md-6 mb-3">
                                <label for="lembaga_akreditasi" class="form-label">Lembaga Akreditasi</label>
                                <input type="text" class="form-control" id="lembaga_akreditasi" name="lembaga_akreditasi"
                                    value="{{ $travelCompany->lembaga_akreditasi }}" required>
                            </div>
                            <!-- Pimpinan -->
                            <div class="col-md-6 mb-3">
                                <label for="Pimpinan" class="form-label">Pimpinan</label>
                                <input type="text" class="form-control" id="Pimpinan" name="Pimpinan"
                                    value="{{ $travelCompany->Pimpinan }}" required>
                            </div>
                            <!-- Telepon -->
                            <div class="col-md-6 mb-3">
                                <label for="Telepon" class="form-label">Telepon</label>
                                <input type="text" class="form-control" id="Telepon" name="Telepon"
                                    value="{{ $travelCompany->Telepon }}" required>
                            </div>
                            <!-- Alamat Kantor Lama -->
                            <div class="col-md-6 mb-3">
                                <label for="alamat_kantor_lama" class="form-label">Alamat Kantor Lama</label>
                                <textarea class="form-control" id="alamat_kantor_lama" name="alamat_kantor_lama" required>{{ $travelCompany->alamat_kantor_lama }}</textarea>
                            </div>
                            <!-- Alamat Kantor Baru -->
                            <div class="col-md-6 mb-3">
                                <label for="alamat_kantor_baru" class="form-label">Alamat Kantor Baru</label>
                                <textarea class="form-control" id="alamat_kantor_baru" name="alamat_kantor_baru" required>{{ $travelCompany->alamat_kantor_baru }}</textarea>
                            </div>
                            <!-- Kab/Kota -->
                            <div class="col-md-6 mb-3">
                                <label for="kab_kota" class="form-label">Kab/Kota</label>
                                <input type="text" class="form-control" id="kab_kota" name="kab_kota"
                                    value="{{ $travelCompany->kab_kota }}" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Perbarui</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
