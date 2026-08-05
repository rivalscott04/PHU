@if ($travelCompany->registration_status && ! $travelCompany->isRegistrationApproved())
    <div class="alert alert-{{ $travelCompany->isRegistrationPending() ? 'warning' : 'danger' }} mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <h6 class="alert-heading mb-2">
                    <i class="bx bx-{{ $travelCompany->isRegistrationPending() ? 'time-five' : 'x-circle' }} me-1"></i>
                    Status Registrasi
                </h6>
                <span class="badge {{ $travelCompany->registration_status->badgeClass() }}">
                    {{ $travelCompany->registration_status->label() }}
                </span>
                @if ($travelCompany->isRegistrationRejected() && $travelCompany->registration_notes)
                    <p class="mb-0 mt-2 small">Alasan penolakan: {{ $travelCompany->registration_notes }}</p>
                @endif
            </div>
            @if ($travelCompany->isRegistrationPending())
                <div class="d-flex flex-wrap gap-2">
                    @if ($travelCompany->hasRegistrationDocument('sk'))
                        <a href="{{ route('travel.registration.document', ['id' => $travelCompany->id, 'type' => 'sk']) }}"
                           target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="bx bx-file me-1"></i> SK / Izin
                        </a>
                    @else
                        <span class="badge bg-danger align-self-center">SK belum ada</span>
                    @endif
                    @if ($travelCompany->hasRegistrationDocument('akreditasi'))
                        <a href="{{ route('travel.registration.document', ['id' => $travelCompany->id, 'type' => 'akreditasi']) }}"
                           target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="bx bx-file me-1"></i> Akreditasi
                        </a>
                    @else
                        <span class="badge bg-danger align-self-center">Akreditasi belum ada</span>
                    @endif
                </div>
            @endif
        </div>
    </div>
@endif

@if ($travelCompany->user)
    <div class="card border mb-4">
        <div class="card-body">
            <h6 class="card-title mb-3">Data PIC (Registrasi)</h6>
            <dl class="row mb-0">
                <dt class="col-sm-3 text-muted fw-normal">Nama</dt>
                <dd class="col-sm-9 mb-2">{{ $travelCompany->user->nama }}</dd>
                <dt class="col-sm-3 text-muted fw-normal">Email</dt>
                <dd class="col-sm-9 mb-2">{{ $travelCompany->user->email }}</dd>
                <dt class="col-sm-3 text-muted fw-normal">HP</dt>
                <dd class="col-sm-9 mb-0">{{ $travelCompany->user->nomor_hp }}</dd>
            </dl>
        </div>
    </div>
@endif
