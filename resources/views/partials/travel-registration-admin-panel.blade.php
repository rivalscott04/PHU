@if ($travelCompany->registration_status && ! $travelCompany->isRegistrationApproved())
    <div class="alert alert-{{ $travelCompany->isRegistrationPending() ? 'warning' : 'danger' }} mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
            <div>
                <strong>Status Registrasi:</strong>
                <span class="badge {{ $travelCompany->registration_status->badgeClass() }} ms-1">
                    {{ $travelCompany->registration_status->label() }}
                </span>
                @if ($travelCompany->isRegistrationRejected() && $travelCompany->registration_notes)
                    <div class="mt-2 small">Alasan penolakan: {{ $travelCompany->registration_notes }}</div>
                @endif
            </div>
            @if ($travelCompany->isRegistrationPending())
                <div class="d-flex gap-2">
                    @if ($travelCompany->hasRegistrationDocument('sk'))
                        <a href="{{ route('travel.registration.document', ['id' => $travelCompany->id, 'type' => 'sk']) }}"
                           target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="bx bx-file me-1"></i> SK / Izin
                        </a>
                    @else
                        <span class="badge bg-danger">SK belum ada</span>
                    @endif
                    @if ($travelCompany->hasRegistrationDocument('akreditasi'))
                        <a href="{{ route('travel.registration.document', ['id' => $travelCompany->id, 'type' => 'akreditasi']) }}"
                           target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="bx bx-file me-1"></i> Akreditasi
                        </a>
                    @else
                        <span class="badge bg-danger">Akreditasi belum ada</span>
                    @endif
                </div>
            @endif
        </div>
    </div>
@endif

@if ($travelCompany->user)
    <div class="card border mb-4">
        <div class="card-body py-3">
            <h6 class="mb-2">Data PIC (Registrasi)</h6>
            <div class="row small">
                <div class="col-md-4"><span class="text-muted">Nama:</span> {{ $travelCompany->user->nama }}</div>
                <div class="col-md-4"><span class="text-muted">Email:</span> {{ $travelCompany->user->email }}</div>
                <div class="col-md-4"><span class="text-muted">HP:</span> {{ $travelCompany->user->nomor_hp }}</div>
            </div>
        </div>
    </div>
@endif
