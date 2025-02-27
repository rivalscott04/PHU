@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-3 d-flex justify-content-between align-items-center">
                    <h6>Data Jamaah</h6>
                    <div>
                        <a href="{{ route('jamaah.umrah.create') }}" class="btn btn-primary btn-md me-2">
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
        $(document).ready(function() {
            // Initialize DataTable with custom pagination
            var table = $('.table').DataTable({
                scrollX: true,
                scrollCollapse: true,
                autoWidth: false,
                dom: '<"d-flex justify-content-between align-items-center px-4 py-3"<"d-flex align-items-center"<"me-2 text-sm">l<"text-sm">>f>t<"d-flex justify-content-between align-items-center px-4 py-3 mt-3"ip>',
                language: {
                    paginate: {
                        previous: "<i class='fas fa-chevron-left'></i>",
                        next: "<i class='fas fa-chevron-right'></i>"
                    },
                    info: "Menampilkan _START_ hingga _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 hingga 0 dari 0 data",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    search: "Cari:",
                    zeroRecords: "Tidak ada data yang ditemukan",
                    infoFiltered: "(disaring dari _MAX_ total entri)"
                },
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                // Add some custom styling after initialization
                initComplete: function() {
                    // Add margin to pagination container
                    $('.dataTables_paginate').addClass('mt-3');

                    // Style the pagination buttons
                    $('.paginate_button').addClass('mx-1');

                    // Ensure proper vertical spacing
                    $('.dataTables_wrapper').css('margin-bottom', '20px');
                }
            });

            // Make sure the table redraws properly when window resizes
            $(window).on('resize', function() {
                table.columns.adjust().draw();
            });

            // Initial column adjustment
            table.columns.adjust().draw();
        });

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
