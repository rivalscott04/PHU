@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header ps-0 d-flex justify-content-between align-items-center">
                    <h6>Data Cabang Travel</h6>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#uploadModal">
                            <i class="bx bx-upload"></i> Import Data
                        </button>
                        <a href="{{ route('form.cabang_travel') }}" class="btn btn-primary">Tambah</a>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table id="table" class="table align-items-center mb-0">
                            <thead>
                                <tr class="text-center">
                                    <th>No.</th>
                                    <th>Travel</th>
                                    <th>Kabupaten</th>
                                    <th>Pusat</th>
                                    <th>Pimpinan Pusat</th>
                                    <th>Alamat Pusat</th>
                                    <th>No SK / BA</th>
                                    <th>Tanggal</th>
                                    <th>Pimpinan Cabang</th>
                                    <th>Alamat Cabang</th>
                                    <th>Telepon</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $item)
                                    <tr class="text-center">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->Penyelenggara }}</td>
                                        <td>{{ $item->kabupaten }}</td>
                                        <td>{{ $item->pusat }}</td>
                                        <td>{{ $item->pimpinan_pusat }}</td>
                                        <td>{{ $item->alamat_pusat }}</td>
                                        <td>{{ $item->SK_BA }}</td>
                                        <td>{{ date('Y-m-d', strtotime($item->tanggal)) }}
                                        </td>
                                        <td>{{ $item->pimpinan_cabang }}</td>
                                        <td>{{ $item->alamat_cabang }}</td>
                                        <td>{{ $item->telepon }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('cabang.travel.edit', $item->id_cabang) }}"
                                                    class="btn btn-sm btn-warning">
                                                    <i class="bx bx-edit"></i>
                                                </a>
                                                <form action="{{ route('cabang.travel.destroy', $item->id_cabang) }}"
                                                    method="POST" style="display: inline;"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
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

    <!-- Modal Upload Data -->
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Import Data Cabang Travel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('import.cabang_travel') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="file" class="form-label">Pilih File Excel</label>
                            <input type="file" class="form-control" id="file" name="file" accept=".xlsx, .xls"
                                required>
                            <div class="form-text">Format file yang didukung: .xlsx, .xls</div>
                        </div>
                        <div class="mb-3">
                            <a href="{{ asset('template/cabang-travel.xlsx') }}" class="text-sm text-decoration-none">
                                <i class="bx bx-download"></i> Download Template Excel
                            </a>
                        </div>
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <small>
                                <strong>Petunjuk:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Download template Excel terlebih dahulu</li>
                                    <li>Isi data sesuai format yang tersedia</li>
                                    <li>Pastikan semua kolom wajib terisi</li>
                                    <li>Upload file yang sudah diisi</li>
                                </ul>
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-upload"></i> Import Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            // Add specific CSS for the No SK/BA column
            $('head').append(`
        <style>
            #travelTable th:nth-child(7),
            #travelTable td:nth-child(7) {
                min-width: 150px !important;
                width: 150px !important;
                white-space: nowrap !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
            }

            /* Force all cells to maintain proper alignment */
            #travelTable th, #travelTable td {
                vertical-align: middle !important;
            }

            /* Ensure proper table layout */
            #travelTable {
                table-layout: fixed !important;
            }
        </style>
    `);

            // Initialize DataTable with modified settings
            var table = $('.table').DataTable({
                scrollX: true,
                scrollCollapse: true,
                autoWidth: false,
                dom: '<"d-flex justify-content-between align-items-center px-4 py-3"<"d-flex align-items-center"<"me-2 text-sm">l<"text-sm">>f>t<"d-flex justify-content-between align-items-center px-4 py-3"ip>',
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
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],
                createdRow: function(row, data, dataIndex) {
                    // Force all SK/BA cells to maintain proper formatting
                    $(row).children().eq(6).css({
                        'min-width': '150px',
                        'width': '150px',
                        'white-space': 'nowrap',
                        'overflow': 'hidden',
                        'text-overflow': 'ellipsis'
                    });
                }
            });

            // Forcefully adjust column widths after initialization
            setTimeout(function() {
                table.columns.adjust().draw();

                // Direct manipulation of the column width
                table.column(6).nodes().each(function(cell, i) {
                    cell.style.minWidth = '150px';
                    cell.style.width = '150px';
                });
            }, 100);

            // Make sure the table redraws properly when window resizes
            $(window).on('resize', function() {
                table.columns.adjust().draw();
            });
        });
    </script>
@endpush
