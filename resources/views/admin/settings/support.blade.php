@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div>
                    <h4 class="mb-sm-0">Kontak Support Sistem</h4>
                    <p class="text-muted mb-0 small">Nomor dan email yang ditampilkan saat pengguna mengalami kendala teknis</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('settings.support.update') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="support_phone" class="form-label">Nomor Telepon / WhatsApp Support</label>
                            <input
                                type="text"
                                class="form-control @error('support_phone') is-invalid @enderror"
                                id="support_phone"
                                name="support_phone"
                                value="{{ old('support_phone', $settings->support_phone ?: $defaults['phone']) }}"
                                placeholder="Contoh: 0812-3456-7890"
                                required
                            >
                            @error('support_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Ditampilkan di footer aplikasi dan halaman login.</small>
                        </div>

                        <div class="mb-4">
                            <label for="support_email" class="form-label">Email Support</label>
                            <input
                                type="email"
                                class="form-control @error('support_email') is-invalid @enderror"
                                id="support_email"
                                name="support_email"
                                value="{{ old('support_email', $settings->support_email ?: $defaults['email']) }}"
                                placeholder="Contoh: support@kemenhaj.go.id"
                                required
                            >
                            @error('support_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info mb-4">
                            <i class="bx bx-info-circle me-1"></i>
                            Kontak ini khusus untuk kendala teknis sistem PANTAU, bukan pengaduan layanan travel.
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bx bx-save me-1"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
