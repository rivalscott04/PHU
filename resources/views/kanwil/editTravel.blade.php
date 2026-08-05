@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="mb-1">Edit Data Travel</h5>
                        <p class="text-muted mb-0 small">{{ $travelCompany->Penyelenggara }}</p>
                    </div>
                    <a href="{{ route('travel', $travelCompany->isRegistrationPending() ? ['filter' => 'pending'] : []) }}"
                       class="btn btn-secondary btn-sm">
                        <i class="bx bx-arrow-back me-1"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    @include('partials.travel-registration-admin-panel', ['travelCompany' => $travelCompany])

                    <form method="POST" action="{{ route('travel.update', $travelCompany->id) }}">
                        @csrf
                        @method('PUT')
                        <p class="text-muted small mb-3"><span class="text-danger">*</span> Wajib diisi</p>
                        @include('partials.travel-company-fields', [
                            'travel' => $travelCompany,
                            'kabupatens' => $kabupatens,
                        ])
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="bx bx-save me-1"></i> Perbarui
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
