@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Impersonate Users</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Impersonate Users</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Travel Users</h4>
                <p class="card-title-desc">Click on a user to impersonate them and see the system from their perspective.</p>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Username</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Travel Company</th>
                                <th>City</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $index => $user)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-xs me-3">
                                            <span class="avatar-title rounded-circle bg-primary">
                                                {{ strtoupper(substr($user->nama, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $user->nama }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->nama }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->travel)
                                        <span class="badge bg-info">{{ $user->travel->Penyelenggara }}</span>
                                    @else
                                        <span class="badge bg-secondary">No Travel Company</span>
                                    @endif
                                </td>
                                <td>{{ $user->city ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('impersonate.take', $user->id) }}" 
                                       class="btn btn-primary btn-sm waves-effect waves-light"
                                       onclick="return confirm('Are you sure you want to impersonate this user?')">
                                        <i class="bx bx-user-check me-1"></i>
                                        Impersonate
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">
                                    <div class="text-muted">
                                        <i class="bx bx-user-x font-size-24 mb-2"></i>
                                        <p>No travel users found</p>
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

<!-- Impersonate Banner -->
@if(app('impersonate')->isImpersonating())
<div class="impersonate-banner" style="position: fixed; top: 0; left: 0; right: 0; background: linear-gradient(45deg, #ff6b6b, #ee5a24); color: white; padding: 10px; text-align: center; z-index: 9999; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-8">
                <strong><i class="bx bx-user-check me-2"></i>You are currently impersonating: {{ auth()->user()->username }}</strong>
                <small class="d-block">You can see the system from this user's perspective</small>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('impersonate.leave') }}" class="btn btn-light btn-sm">
                    <i class="bx bx-log-out me-1"></i>
                    Stop Impersonating
                </a>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('styles')
<style>
    .impersonate-banner {
        animation: slideDown 0.3s ease-out;
    }
    
    @keyframes slideDown {
        from {
            transform: translateY(-100%);
        }
        to {
            transform: translateY(0);
        }
    }
    
    .table th {
        background-color: #f8f9fa;
        border-color: #dee2e6;
        font-weight: 600;
    }
    
    .avatar-xs {
        width: 32px;
        height: 32px;
        font-size: 14px;
        line-height: 32px;
    }
    
    .badge {
        font-size: 11px;
        padding: 4px 8px;
    }
</style>
@endpush 