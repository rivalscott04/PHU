@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header ps-0 d-flex justify-content-between align-items-center">
                    <h6>Data Travel</h6>
                    <div>
                        <a href="{{ route('form.travel') }}" class="btn btn-primary me-2">Tambah</a>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#uploadModal">
                            <i class="bx bx-upload me-1"></i> Upload Excel
                        </button>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <!-- DataTables length and search controls -->
                        <div class="d-flex justify-content-between align-items-center px-4 py-3">
                            <div class="d-flex align-items-center">
                                <label class="me-2 text-sm">Tampilkan</label>
                                <select id="dataTable_length" class="form-select form-select-sm me-2" style="width: 70px">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                                <span class="text-sm">data per halaman</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <label class="me-2 text-sm">Cari:</label>
                                <input type="search" id="dataTable_search" class="form-control form-control-sm"
                                    style="width: 200px">
                            </div>
                        </div>

                        <table id="dataTable" class="table align-items-center mb-0">
                            <thead>
                                <tr class="text-center">
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                        rowspan="2" style="width: 5%">
                                        <div class="vertical-text">No.</div>
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                        rowspan="2" style="width: 15%">
                                        Penyelenggara
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                        colspan="6">
                                        Nomor SK
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                        rowspan="2" style="width: 10%">
                                        Pimpinan
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                        rowspan="2" style="width: 15%">
                                        Alamat Kantor Lama
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                        rowspan="2" style="width: 15%">
                                        Alamat Kantor Baru
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                        rowspan="2" style="width: 10%">
                                        Telepon
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                        rowspan="2" style="width: 5%">
                                        Status
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                        rowspan="2" style="width: 10%">
                                        Kab/Kota
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                        rowspan="2" style="width: 10%">
                                        Aksi
                                    </th>
                                </tr>
                                <tr class="text-center">
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Pusat
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Tanggal
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Jml Akre
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Tanggal Akredi
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Lembaga Akred
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        -
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr class="text-center">
                                        <td class="text-sm font-weight-bold">{{ $loop->iteration }}</td>
                                        <td class="text-sm font-weight-bold">{{ $item->Penyelenggara }}</td>
                                        <td class="text-sm font-weight-bold">{{ $item->Pusat }}</td>
                                        <td class="text-sm font-weight-bold">
                                            {{ date('d/m/Y', strtotime($item->Tanggal)) }}
                                        </td>
                                        <td class="text-sm font-weight-bold">{{ $item->nilai_akreditasi }}</td>
                                        <td class="text-sm font-weight-bold">
                                            {{ date('d/m/Y', strtotime($item->tanggal_akreditasi)) }}
                                        </td>
                                        <td class="text-sm font-weight-bold">{{ $item->lembaga_akreditasi }}</td>
                                        <td class="text-sm font-weight-bold">-</td>
                                        <td class="text-sm font-weight-bold">{{ $item->Pimpinan }}</td>
                                        <td class="text-sm font-weight-bold">{{ $item->alamat_kantor_lama }}</td>
                                        <td class="text-sm font-weight-bold">{{ $item->alamat_kantor_baru }}</td>
                                        <td class="text-sm font-weight-bold">{{ $item->Telepon }}</td>
                                        <td class="text-sm font-weight-bold text-center">
                                            {{ $item->Status }}
                                        </td>
                                        <td class="text-sm font-weight-bold">{{ $item->kab_kota }}</td>
                                        <td class="text-sm font-weight-bold">
                                            <a href="{{ route('travel.edit', $item->id) }}" class="btn btn-sm btn-warning"
                                                title="Edit">
                                                <i class="bx bx-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- DataTables pagination -->
                        <div class="d-flex justify-content-between align-items-center px-4 py-3">
                            <div id="dataTable_info" class="text-sm text-secondary"></div>
                            <div id="dataTable_paginate" class="pagination"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Upload Data User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('import.data') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="file" class="form-label">Pilih File Excel</label>
                            <input type="file" class="form-control" id="file" name="file"
                                accept=".xlsx, .xls" required>
                        </div>
                        <div class="mb-3">
                            <a href="{{ route('travel.template') }}" class="text-sm">
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
    <!-- Initialize DataTables -->
    <script>
        $(document).ready(function() {
            // Initialize DataTable with custom DOM and scrolling
            var table = $('#dataTable').DataTable({
                // Change responsive to false and use scrollX instead
                responsive: false,
                scrollX: true, // Enable horizontal scrolling
                scrollCollapse: true,
                dom: 't', // Only show table
                language: {
                    paginate: {
                        previous: "<i class='fa fa-angle-left'></i>",
                        next: "<i class='fa fa-angle-right'></i>"
                    },
                    info: "Menampilkan _START_ hingga _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 hingga 0 dari 0 data",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    search: "Cari:",
                    zeroRecords: "Tidak ada data yang ditemukan",
                    infoFiltered: "(disaring dari _MAX_ total data)"
                },
                columnDefs: [{
                    orderable: false,
                    targets: -1
                }], // Disable sorting on action column
                "drawCallback": function(settings) {
                    // Update info text
                    var info = this.api().page.info();
                    $('#dataTable_info').html('Menampilkan ' + (info.start + 1) + ' hingga ' + info
                        .end + ' dari ' + info.recordsTotal + ' data');

                    // Build custom pagination
                    var paginationHtml = '';
                    var pages = this.api().page.info().pages;
                    var currentPage = this.api().page.info().page;

                    paginationHtml += '<ul class="pagination pagination-sm mb-0">';

                    // Previous button
                    paginationHtml += '<li class="page-item' + (currentPage === 0 ? ' disabled' : '') +
                        '">';
                    paginationHtml +=
                        '<a class="page-link" href="#" data-page="prev"><i class="fas fa-chevron-left"></i></a></li>';

                    // Page numbers
                    var startPage = Math.max(0, currentPage - 2);
                    var endPage = Math.min(pages - 1, currentPage + 2);

                    for (var i = startPage; i <= endPage; i++) {
                        paginationHtml += '<li class="page-item' + (i === currentPage ? ' active' :
                            '') + '">';
                        paginationHtml += '<a class="page-link" href="#" data-page="' + i + '">' + (i +
                            1) + '</a></li>';
                    }

                    // Next button
                    paginationHtml += '<li class="page-item' + (currentPage === pages - 1 ?
                        ' disabled' : '') + '">';
                    paginationHtml +=
                        '<a class="page-link" href="#" data-page="next"><i class="fas fa-chevron-right"></i></a></li>';

                    paginationHtml += '</ul>';

                    $('#dataTable_paginate').html(paginationHtml);

                    // Add event listeners to pagination
                    $('#dataTable_paginate .page-link').on('click', function(e) {
                        e.preventDefault();
                        var page = $(this).data('page');

                        if (page === 'prev') {
                            table.page('previous').draw('page');
                        } else if (page === 'next') {
                            table.page('next').draw('page');
                        } else {
                            table.page(page).draw('page');
                        }
                    });
                }
            });

            // Make sure the table redraws properly when window resizes
            $(window).on('resize', function() {
                table.columns.adjust().draw();
            });

            // Custom length change
            $('#dataTable_length').on('change', function() {
                table.page.len($(this).val()).draw();
            });

            // Custom search
            $('#dataTable_search').on('keyup', function() {
                table.search(this.value).draw();
            });
        });
    </script>
@endpush
