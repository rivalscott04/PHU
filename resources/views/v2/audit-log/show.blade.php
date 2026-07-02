@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="mb-1">Detail Aktivitas</h4>
                <p class="text-muted mb-0">{{ $log->created_at?->format('d F Y, H:i') }} WIB</p>
            </div>
            <a href="{{ route('v2.audit-log.index') }}" class="btn btn-sm btn-secondary">Kembali</a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-3">
            <div class="card h-100">
                <div class="card-header"><h5 class="mb-0">Ringkasan</h5></div>
                <div class="card-body">
                    <p class="fs-5 mb-3">{{ $narrative['summary'] }}</p>
                    @if($narrative['detail'])
                        <p class="text-muted mb-0">{{ $narrative['detail'] }}.</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-3">
            <div class="card h-100">
                <div class="card-header"><h5 class="mb-0">Informasi Pelaku</h5></div>
                <div class="card-body">
                    <dl class="mb-0">
                        <dt>Nama</dt>
                        <dd>{{ $narrative['actor'] }}</dd>
                        <dt>Peran</dt>
                        <dd>{{ $narrative['actor_role'] }}</dd>
                        <dt>Jenis kegiatan</dt>
                        <dd>{{ $narrative['category'] }}</dd>
                        @if($log->user?->email)
                            <dt>Email</dt>
                            <dd>{{ $log->user->email }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
