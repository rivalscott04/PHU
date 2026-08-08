@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-sm-0">Paket Umrah Saya</h4>
                    <p class="text-muted mb-0 small">Kelola katalog harga paket untuk mempercepat pengisian BA Pemberangkatan</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('form.bap') }}" class="btn btn-sm btn-outline-primary">
                        <i class="bx bx-file me-1"></i> Ajukan BA
                    </a>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#packageModal"
                        onclick="openPackageModal()">
                        <i class="bx bx-plus me-1"></i> Tambah Paket
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Katalog Paket</h5>
                </div>
                <div class="card-body px-0 pt-0">
                    @if ($packages->isEmpty())
                        <div class="text-center py-5 px-3">
                            <i class="bx bx-package display-4 text-muted"></i>
                            <p class="text-muted mt-3 mb-2">Belum ada paket tersimpan.</p>
                            <p class="text-muted small mb-3">Buat paket standar agar harga dan durasi terisi otomatis saat mengajukan BA.</p>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#packageModal"
                                onclick="openPackageModal()">
                                <i class="bx bx-plus me-1"></i> Tambah Paket Pertama
                            </button>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Nama Paket</th>
                                        <th class="text-end">Harga/Orang</th>
                                        <th class="text-center">Hari</th>
                                        <th>Maskapai Default</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-end pe-3">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($packages as $package)
                                        <tr>
                                            <td class="ps-3">
                                                <div class="fw-medium">{{ $package->name }}</div>
                                                @if ($package->service_notes)
                                                    <small class="text-muted d-block">{{ \Illuminate\Support\Str::limit($package->service_notes, 80) }}</small>
                                                @endif
                                            </td>
                                            <td class="text-end">Rp {{ number_format($package->price, 0, ',', '.') }}</td>
                                            <td class="text-center">{{ $package->days ?: '-' }}</td>
                                            <td>{{ $package->default_airline ?: '-' }}</td>
                                            <td class="text-center">
                                                @if ($package->is_active)
                                                    <span class="badge bg-success">Aktif</span>
                                                @else
                                                    <span class="badge bg-secondary">Nonaktif</span>
                                                @endif
                                            </td>
                                            <td class="text-end pe-3">
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                    onclick='openPackageModal(@json($package))'>
                                                    <i class="bx bx-edit"></i>
                                                </button>
                                                <form action="{{ route('travel.packages.destroy', $package) }}" method="POST"
                                                    class="d-inline" onsubmit="return confirm('Hapus paket ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Riwayat Harga BA</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">10 pengajuan BA terakhir dengan harga per orang.</p>
                    @if ($priceHistory->isEmpty())
                        <p class="text-muted small mb-0">Belum ada riwayat. Harga akan muncul setelah Anda mengajukan BA Pemberangkatan.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th class="text-end">Harga</th>
                                        <th class="text-center">Jamaah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($priceHistory as $item)
                                        <tr>
                                            <td>
                                                <div>{{ $item->datetime ? \Carbon\Carbon::parse($item->datetime)->format('d/m/Y') : '-' }}</div>
                                                @if ($item->package)
                                                    <small class="text-muted">{{ $item->package }}</small>
                                                @endif
                                            </td>
                                            <td class="text-end">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                            <td class="text-center">{{ $item->people }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h6 class="mb-2"><i class="bx bx-info-circle me-1"></i>Tips</h6>
                    <ul class="text-muted small mb-0 ps-3">
                        <li>Harga per orang, bukan total keseluruhan.</li>
                        <li>Saat ajukan BA, pilih paket untuk mengisi harga otomatis.</li>
                        <li>Rincian layanan membantu jamaah memahami komponen biaya.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="packageModal" tabindex="-1" aria-labelledby="packageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="packageForm" method="POST">
                    @csrf
                    <div id="packageMethodField"></div>
                    <div class="modal-header">
                        <h5 class="modal-title" id="packageModalLabel">Tambah Paket</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="package_name" class="form-label">Nama Paket <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="package_name" name="name" required
                                    placeholder="Contoh: Umrah Reguler 9 Hari">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="package_price_display" class="form-label">Harga per Orang <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="package_price_display" required
                                    placeholder="Contoh: 25.000.000" oninput="formatPackagePrice(this)">
                                <input type="hidden" id="package_price" name="price">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="package_days" class="form-label">Durasi (Hari)</label>
                                <input type="number" class="form-control" id="package_days" name="days" min="1" max="365"
                                    placeholder="Contoh: 9">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="package_airline" class="form-label">Maskapai Default</label>
                                <select class="form-select" id="package_airline" name="default_airline">
                                    <option value="">Pilih maskapai (opsional)</option>
                                    @foreach ($airlineOptions as $airlineName)
                                        <option value="{{ $airlineName }}">{{ $airlineName }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="package_notes" class="form-label">Rincian Layanan</label>
                                <textarea class="form-control" id="package_notes" name="service_notes" rows="3"
                                    placeholder="Contoh: Konsumsi, transportasi, manasik, petugas, asuransi"></textarea>
                                <small class="text-muted">Sesuai edukasi Kanwil: layanan harus jelas dan rasional.</small>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="package_active" name="is_active" value="1" checked>
                                    <label class="form-check-label" for="package_active">Paket aktif (tampil saat ajukan BA)</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        const packageStoreUrl = @json(route('travel.packages.store'));
        const packageUpdateUrlTemplate = @json(route('travel.packages.update', ['package' => '__ID__']));

        function formatPackagePrice(input) {
            const rawValue = input.value.replace(/[^0-9]/g, '');
            input.value = rawValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            document.getElementById('package_price').value = rawValue;
        }

        function openPackageModal(pkg = null) {
            const form = document.getElementById('packageForm');
            const methodField = document.getElementById('packageMethodField');
            const title = document.getElementById('packageModalLabel');

            form.action = pkg ? packageUpdateUrlTemplate.replace('__ID__', pkg.id) : packageStoreUrl;
            methodField.innerHTML = pkg ? '<input type="hidden" name="_method" value="PUT">' : '';
            title.textContent = pkg ? 'Ubah Paket' : 'Tambah Paket';

            document.getElementById('package_name').value = pkg?.name ?? '';
            document.getElementById('package_price_display').value = pkg?.price
                ? Number(pkg.price).toLocaleString('id-ID')
                : '';
            document.getElementById('package_price').value = pkg?.price ?? '';
            document.getElementById('package_days').value = pkg?.days ?? '';
            document.getElementById('package_airline').value = pkg?.default_airline ?? '';
            document.getElementById('package_notes').value = pkg?.service_notes ?? '';
            document.getElementById('package_active').checked = pkg ? Boolean(pkg.is_active) : true;
        }

        document.getElementById('packageForm').addEventListener('submit', function () {
            const display = document.getElementById('package_price_display');
            const hidden = document.getElementById('package_price');
            if (display && hidden) {
                hidden.value = display.value.replace(/[^0-9]/g, '');
            }
        });
    </script>
@endpush
