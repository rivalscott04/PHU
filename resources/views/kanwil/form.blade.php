@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Dashboard'])

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h5 class="mb-0">Form Data</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('post.form') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="penyelenggara" class="form-label">Penyelenggara</label>
                                    <input type="text" class="form-control" id="penyelenggara" name="penyelenggara"
                                        required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="nomor_sk" class="form-label">Nomor SK</label>
                                    <input type="text" class="form-control" id="nomor_sk" name="nomor_sk" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="tanggal_sk" class="form-label">Tanggal SK</label>
                                    <input type="date" class="form-control" id="tanggal_sk" name="tanggal_sk" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="akreditasi" class="form-label">Akreditasi</label>
                                    <input type="text" class="form-control" id="akreditasi" name="akreditasi" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="tanggal_akreditasi" class="form-label">Tanggal Akreditasi</label>
                                    <input type="date" class="form-control" id="tanggal_akreditasi"
                                        name="tanggal_akreditasi" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="lembaga_akreditasi" class="form-label">Lembaga Akreditasi</label>
                                    <input type="text" class="form-control" id="lembaga_akreditasi"
                                        name="lembaga_akreditasi" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="pimpinan" class="form-label">Pimpinan</label>
                                    <input type="text" class="form-control" id="pimpinan" name="pimpinan" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="telepon" class="form-label">Telepon</label>
                                    <input type="text" class="form-control" id="telepon" name="telepon" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="kab_kota" class="form-label">Kab/Kota</label>
                                    <input type="text" class="form-control" id="kab_kota" name="kab_kota" required>
                                </div>
                                <div class="col-md-6 mb-3">

                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="alamat_kantor_lama" class="form-label">Alamat Kantor Lama</label>
                                    <textarea class="form-control" id="alamat_kantor_lama" name="alamat_kantor_lama" rows="3" required></textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="alamat_kantor_baru" class="form-label">Alamat Kantor Baru</label>
                                    <textarea class="form-control" id="alamat_kantor_baru" name="alamat_kantor_baru" rows="3" required></textarea>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.footers.auth.footer')
@endsection
