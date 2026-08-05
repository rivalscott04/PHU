@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Data Travel</h5>
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
                        <button type="submit" class="btn btn-primary">Perbarui</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
