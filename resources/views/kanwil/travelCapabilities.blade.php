@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Travel Capabilities Management</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Travel Capabilities</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Travel Companies & Their Capabilities</h4>
                <p class="card-title-desc">Manage travel company capabilities and services</p>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Travel Company</th>
                                <th>Type</th>
                                <th>Capabilities</th>
                                <th>License Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($travelCompanies as $index => $travel)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-xs me-3">
                                            <span class="avatar-title rounded-circle bg-primary">
                                                {{ strtoupper(substr($travel->Penyelenggara, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $travel->Penyelenggara }}</h6>
                                            <small class="text-muted">{{ $travel->Pusat }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($travel->Status === 'PIHK')
                                        <span class="badge bg-success">PIHK</span>
                                        <small class="d-block text-muted">Haji & Umrah</small>
                                    @else
                                        <span class="badge bg-info">PPIU</span>
                                        <small class="d-block text-muted">Umrah Only</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        @if($travel->canHandleHaji())
                                            <span class="badge bg-success">
                                                <i class="bx bx-check me-1"></i>Haji
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="bx bx-x me-1"></i>Haji
                                            </span>
                                        @endif
                                        
                                        @if($travel->canHandleUmrah())
                                            <span class="badge bg-success">
                                                <i class="bx bx-check me-1"></i>Umrah
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="bx bx-x me-1"></i>Umrah
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($travel->getLicenseStatus() === 'Active')
                                        <span class="badge bg-success">
                                            <i class="bx bx-check-circle me-1"></i>Active
                                        </span>
                                        <small class="d-block text-muted">Expires: {{ $travel->license_expiry?->format('d M Y') }}</small>
                                    @elseif($travel->getLicenseStatus() === 'Expired')
                                        <span class="badge bg-danger">
                                            <i class="bx bx-x-circle me-1"></i>Expired
                                        </span>
                                        <small class="d-block text-muted">Expired: {{ $travel->license_expiry?->format('d M Y') }}</small>
                                    @else
                                        <span class="badge bg-warning">
                                            <i class="bx bx-question-circle me-1"></i>No License
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-primary btn-sm" 
                                                onclick="editCapabilities({{ $travel->id }})"
                                                title="Edit Capabilities">
                                            <i class="bx bx-edit me-1"></i>
                                            Edit
                                        </button>
                                        <button class="btn btn-info btn-sm" 
                                                onclick="viewDetails({{ $travel->id }})"
                                                title="View Details">
                                            <i class="bx bx-show me-1"></i>
                                            Details
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">
                                    <div class="text-muted">
                                        <i class="bx bx-building-house font-size-24 mb-2"></i>
                                        <p>No travel companies found</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Capabilities Summary -->
<div class="row">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="bx bx-building-house font-size-24"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h4 class="mb-1">{{ $totalTravel }}</h4>
                        <p class="mb-0">Total Travel Companies</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="bx bx-check-circle font-size-24"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h4 class="mb-1">{{ $pihkCount }}</h4>
                        <p class="mb-0">PIHK Companies</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="bx bx-plane font-size-24"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h4 class="mb-1">{{ $ppiuCount }}</h4>
                        <p class="mb-0">PPIU Companies</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')
<script>
function editCapabilities(travelId) {
    // Implement edit capabilities modal
    Swal.fire({
        title: 'Edit Travel Capabilities',
        text: 'This feature will be implemented soon',
        icon: 'info',
        confirmButtonText: 'OK'
    });
}

function viewDetails(travelId) {
    // Implement view details modal
    Swal.fire({
        title: 'Travel Details',
        text: 'This feature will be implemented soon',
        icon: 'info',
        confirmButtonText: 'OK'
    });
}
</script>
@endpush 