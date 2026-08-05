@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            @include('partials.bap-module-info', ['variant' => 'pemberangkatan'])
            @if($guide = \App\Support\RoleWorkflowGuide::for('bap_list'))
                @include('partials.workflow-guide', ['guide' => $guide])
            @endif

            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-sm-0">BA Pemberangkatan</h4>
                    <p class="text-muted mb-0 small">Daftar pengajuan keberangkatan jamaah</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    @if (auth()->user()->role === 'admin' || auth()->user()->role === 'kabupaten')
                        <a href="{{ route('verify-e-sign') }}" class="btn btn-sm btn-info">
                            <i class="bx bx-qr-scan me-1"></i> Verifikasi E Sign
                        </a>
                    @endif
                    <a href="{{ route('form.bap') }}" onclick="return checkJamaah({{ $jamaahCount }});"
                        class="btn btn-sm btn-primary">
                        <i class="bx bx-plus me-1"></i> Tambah
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Daftar Pengajuan</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th>No.</th>
                                <th>Nama</th>
                                <th>Jabatan</th>
                                <th>PPIU</th>
                                <th>Nomor HP</th>
                                <th>Kab/Kota</th>
                                <th>Tgl Berangkat</th>
                                <th>Jumlah Jamaah</th>
                                <th>Harga/Orang</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $item)
                                <tr class="text-center">
                                        <td>{{ $data->firstItem() + $loop->index }}</td>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->jabatan }}</td>
                                        <td>{{ $item->ppiuname }}</td>
                                        <td>{{ $item->address_phone }}</td>
                                        <td>{{ $item->kab_kota }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item->datetime)->format('d/m/Y') }}</td>
                                        <td>{{ $item->people }}</td>
                                        {{-- <td>{{ $item->package }}</td> --}}
                                        <td><span>Rp.
                                            </span>{{ number_format($item->price, 2, ',', '.') }}</td>
                                        <td>
                                            @if (auth()->user()->role === 'admin' || auth()->user()->role === 'kabupaten')
                                                <form action="{{ route('bap.updateStatus', $item->id) }}" method="POST"
                                                    id="statusForm{{ $item->id }}">
                                                    @csrf
                                                    <div class="d-flex flex-column gap-1">
                                                        <select name="status"
                                                            class="form-select form-select-sm {{ $item->status == 'diajukan' ? 'bg-primary text-white fw-semibold' : '' }}
                                                                {{ $item->status == 'diproses' ? 'bg-warning text-dark fw-semibold' : '' }}
                                                                {{ $item->status == 'diterima' ? 'bg-success text-white fw-semibold' : '' }}"
                                                            onchange="handleStatusChange({{ $item->id }}, this.value)">
                                                            <option value="pending"
                                                                {{ $item->status == 'pending' ? 'selected' : '' }}>Pending
                                                            </option>
                                                            <option value="diajukan"
                                                                {{ $item->status == 'diajukan' ? 'selected' : '' }}>
                                                                Diajukan
                                                            </option>
                                                            <option value="diproses"
                                                                {{ $item->status == 'diproses' ? 'selected' : '' }}>
                                                                Diproses
                                                            </option>
                                                            <option value="diterima"
                                                                {{ $item->status == 'diterima' ? 'selected' : '' }}>
                                                                Diterima
                                                            </option>
                                                        </select>
                                                        @if ($item->status === 'diterima' && $item->nomor_surat)
                                                            <small class="text-muted">{{ $item->nomor_surat }}</small>
                                                        @endif
                                                    </div>
                                                </form>
                                            @else
                                                @php $badge = \App\Support\BapWizardStatus::travelBadge($item); @endphp
                                                <div>
                                                    <span class="badge {{ $badge['class'] }}">
                                                        {{ $badge['label'] }}
                                                    </span>
                                                    @if ($item->status === 'diterima' && $item->nomor_surat)
                                                        <small class="text-muted d-block mt-1">{{ $item->nomor_surat }}</small>
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2 justify-content-center align-items-center">
                                                @if (auth()->user()->role === 'user' && ($wizardRoute = \App\Support\BapWizardStatus::wizardRouteName($item)))
                                                    <a href="{{ route($wizardRoute, $item->id) }}"
                                                        class="btn btn-sm btn-warning" title="Lanjutkan pengajuan">
                                                        Lanjutkan
                                                    </a>
                                                @endif
                                                <a href="{{ route('detail.bap', $item->id) }}" class="btn btn-sm btn-outline-primary" title="Detail">
                                                    <i class="bx bx-info-circle"></i>
                                                </a>
                                                @if ($item->status === 'diterima')
                                                    <a href="{{ route('cetak.bap', $item->id) }}" target="_blank"
                                                        class="btn btn-sm btn-outline-success" title="Cetak BAP">
                                                        <i class="bx bx-printer"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                    </table>
                </div>
                @if ($data->hasPages())
                    <div class="px-3 py-2 border-top">
                        {{ $data->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function checkJamaah(jamaahCount) {
            if (jamaahCount == 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Maaf terjadi Kesalahan',
                    text: 'Silahkan mengisi menu jamaah terlebih dahulu.',
                    confirmButtonText: 'Tutup'
                });
                return false;
            }
            return true;
        }

        function handleStatusChange(itemId, status) {
            const form = document.getElementById('statusForm' + itemId);

            // Submit form langsung untuk semua status
            form.submit();
        }
    </script>
@endpush
