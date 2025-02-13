@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-3 d-flex justify-content-between align-items-center">
                    <h6>Data Jamaah</h6>
                    <div>
                        <a href="{{ route('jamaah.haji.create') }}" class="btn btn-primary btn-md me-2">
                            <i class="bx bx-plus me-1"></i> Tambah
                        </a>
                        <button type="button" class="btn btn-success btn-md" data-bs-toggle="modal"
                            data-bs-target="#uploadModal">
                            <i class="bx bx-upload me-1"></i> Upload Excel
                        </button>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr class="text-center">
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        No</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Nama</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Alamat</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        No HP</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                        style="width: 200px; min-width: 200px;">
                                        NIK</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($jamaah as $key => $item)
                                    <tr class="text-center">
                                        <td class="text-sm font-weight-bold">{{ $key + 1 }}</td>
                                        <td class="text-sm font-weight-bold">{{ $item->nama }}</td>
                                        <td class="text-sm font-weight-bold">{{ $item->alamat }}</td>
                                        <td class="text-sm font-weight-bold">{{ $item->nomor_hp }}</td>
                                        <td class="text-sm font-weight-bold" style="width: 200px; min-width: 200px;">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <span id="nik_{{ $item->id }}"
                                                    data-nik="{{ $item->nik }}">{{ str_repeat('*', strlen($item->nik)) }}</span>
                                                <button class="btn btn-link p-0 ms-2"
                                                    onclick="toggleNik('{{ $item->id }}')">
                                                    <i id="icon_{{ $item->id }}" class="bx bxs-show"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="text-lg font-weight-bold">
                                            <a href="{{ route('jamaah.detail', $item->id) }}">
                                                <i class="bx bx-info-circle me-2"></i>
                                            </a>
                                            <a href="{{ route('jamaah.edit', $item->id) }}">
                                                <i class="bx bx-edit text-success"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Upload Data Jamaah</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('jamaah.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="file" class="form-label">Pilih File Excel</label>
                            <input type="file" class="form-control" id="file" name="file" accept=".xlsx, .xls"
                                required>
                        </div>
                        <div class="mb-3">
                            <a href="{{ route('jamaah.template') }}" class="text-sm">
                                <i class="bx bx-download"></i> Download Template Excel
                            </a>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        function toggleNik(id) {
            const nikElement = document.getElementById(`nik_${id}`);
            const iconElement = document.getElementById(`icon_${id}`);
            const nik = nikElement.dataset.nik;
            const hiddenNik = '*'.repeat(nik.length);

            if (nikElement.textContent === hiddenNik) {
                nikElement.textContent = nik;
                iconElement.classList.remove('bxs-show');
                iconElement.classList.add('bxs-hide');
            } else {
                nikElement.textContent = hiddenNik;
                iconElement.classList.remove('bxs-hide');
                iconElement.classList.add('bxs-show');
            }
        }
    </script>
@endpush
