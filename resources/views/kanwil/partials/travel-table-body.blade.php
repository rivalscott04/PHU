@forelse ($data as $item)
    <tr class="text-center align-middle" data-travel-id="{{ $item->id }}">
        <td>{{ $data->firstItem() + $loop->index }}</td>
        <td class="text-start">{{ $item->Penyelenggara }}</td>
        <td>{{ $item->Pusat }}</td>
        <td>{{ $item->Tanggal ? date('d/m/Y', strtotime($item->Tanggal)) : '-' }}</td>
        <td>{{ $item->nilai_akreditasi }}</td>
        <td>{{ $item->tanggal_akreditasi ? date('d/m/Y', strtotime($item->tanggal_akreditasi)) : '-' }}</td>
        <td>{{ $item->lembaga_akreditasi }}</td>
        <td>-</td>
        <td>{{ $item->Pimpinan }}</td>
        <td class="text-start">{{ $item->alamat_kantor_lama }}</td>
        <td class="text-start">{{ $item->alamat_kantor_baru }}</td>
        <td>{{ $item->Telepon }}</td>
        <td>
            <div class="d-flex flex-column align-items-center status-badge">
                <span class="badge {{ $item->Status === 'PIHK' ? 'bg-success' : 'bg-info' }}">{{ $item->Status }}</span>
                <small class="text-muted mt-1">{{ $item->Status === 'PIHK' ? 'Haji & Umrah' : 'Umrah Only' }}</small>
            </div>
        </td>
        <td>{{ $item->kab_kota }}</td>
        <td>
            @php
                $regStatus = $item->registration_status ?? \App\Enums\TravelRegistrationStatus::Approved;
            @endphp
            <span class="badge {{ $regStatus->badgeClass() }}">{{ $regStatus->label() }}</span>
            @if ($regStatus === \App\Enums\TravelRegistrationStatus::Pending && $item->user)
                <div class="mt-1">
                    <small class="text-muted d-block">{{ $item->user->nama }}</small>
                    <small class="text-muted d-block">{{ $item->user->email }}</small>
                </div>
                @if ($item->dokumen_sk || $item->dokumen_akreditasi)
                    <div class="mt-2 d-flex flex-column gap-1">
                        @if ($item->dokumen_sk)
                            @include('partials.document-preview-button', [
                                'url' => route('travel.registration.document', ['id' => $item->id, 'type' => 'sk']),
                                'path' => $item->dokumen_sk,
                                'label' => 'SK / Izin',
                            ])
                        @endif
                        @if ($item->dokumen_akreditasi)
                            @include('partials.document-preview-button', [
                                'url' => route('travel.registration.document', ['id' => $item->id, 'type' => 'akreditasi']),
                                'path' => $item->dokumen_akreditasi,
                                'label' => 'Akreditasi',
                            ])
                        @endif
                    </div>
                @endif
            @endif
            @if ($regStatus === \App\Enums\TravelRegistrationStatus::Rejected && $item->registration_notes)
                <small class="text-danger d-block mt-1">{{ Str::limit($item->registration_notes, 60) }}</small>
            @endif
        </td>
        <td>
            <div class="d-flex justify-content-center gap-1 flex-wrap">
                @if (auth()->user()->role === 'admin' && ($item->registration_status ?? null) === \App\Enums\TravelRegistrationStatus::Pending)
                    <form method="POST" action="{{ route('travel.registration.approve', $item->id) }}" class="d-inline" id="approve-form-{{ $item->id }}">
                        @csrf
                        <button type="button" class="btn btn-success btn-sm" title="Setujui"
                            onclick='confirmApproveRegistration(document.getElementById("approve-form-{{ $item->id }}"), @json($item->Penyelenggara))'>
                            <i class="bx bx-check me-1"></i> Setujui
                        </button>
                    </form>
                    <button type="button" class="btn btn-danger btn-sm"
                        onclick='openRejectModal({{ $item->id }}, @json($item->Penyelenggara))' title="Tolak">
                        <i class="bx bx-x me-1"></i> Tolak
                    </button>
                @endif
                <button type="button" class="btn btn-primary btn-sm"
                    onclick='editStatus({{ $item->id }}, @json($item->Status), @json($item->Penyelenggara))' title="Update Status">
                    <i class="bx bx-edit me-1"></i> Status
                </button>
                <a href="{{ route('travel.edit', $item->id) }}" class="btn btn-sm btn-warning" title="Edit">
                    <i class="bx bx-edit"></i>
                </a>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="15" class="text-center py-4 text-muted">Tidak ada data PPIU ditemukan</td>
    </tr>
@endforelse
