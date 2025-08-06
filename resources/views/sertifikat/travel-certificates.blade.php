@extends('layouts.app')

@section('title', 'Sertifikat PPIU - Travel Company')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <h4 class="mb-0">Sertifikat PPIU Saya</h4>
                <div class="page-title-right">
                    <a href="{{ route('home') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bx bx-certificate text-primary"></i>
                        Daftar Sertifikat PPIU
                    </h5>
                </div>
                <div class="card-body">
                    @if($sertifikat->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nomor Surat</th>
                                        <th>Nomor Dokumen</th>
                                        <th>Tanggal Diterbitkan</th>
    
                                        <th>Status</th>
                                        <th>Lokasi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sertifikat as $index => $cert)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $cert->nomor_surat }}</td>
                                            <td>{{ $cert->nomor_dokumen }}</td>
                                            <td>{{ $cert->tanggal_diterbitkan->format('d F Y') }}</td>
        
                                            <td>
                                                <span class="badge bg-{{ $cert->getStatusColor() }}">
                                                    {{ $cert->getStatusText() }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $cert->jenis_lokasi == 'pusat' ? 'primary' : 'warning' }}">
                                                    {{ ucfirst($cert->jenis_lokasi) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($cert->pdf_path)
                                                    <a href="{{ route('sertifikat.download', $cert->id) }}" 
                                                       class="btn btn-sm btn-primary" 
                                                       title="Unduh PDF">
                                                        <i class="bx bx-download"></i> PDF
                                                    </a>
                                                @else
                                                    <a href="{{ route('sertifikat.generate', $cert->id) }}" 
                                                       class="btn btn-sm btn-success" 
                                                       title="Generate PDF">
                                                        <i class="bx bx-file-plus"></i> Generate
                                                    </a>
                                                @endif
                                                
                                                <a href="{{ route('sertifikat.verifikasi', $cert->uuid) }}" 
                                                   class="btn btn-sm btn-info" 
                                                   target="_blank"
                                                   title="Verifikasi">
                                                    <i class="bx bx-search"></i> Verifikasi
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-center">
                            {{ $sertifikat->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bx bx-certificate text-muted" style="font-size: 4rem;"></i>
                            <h5 class="mt-3 text-muted">Belum ada sertifikat</h5>
                            <p class="text-muted">Sertifikat PPIU Anda akan muncul di sini setelah dibuat oleh admin.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 