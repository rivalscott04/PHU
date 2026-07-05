@php
    use App\Support\DashboardExecutive;
@endphp

<div class="card mb-3" id="v2-dashboard-warnings">
    <div class="card-header bg-transparent">
        <h5 class="mb-0 text-danger"><i class="bx bx-error-circle me-1"></i> Early Warning</h5>
    </div>
    <div class="card-body" id="warning-list">
        @forelse ($warnings as $warning)
            <div class="alert alert-{{ DashboardExecutive::warningAlertClass($warning['level'] ?? 'info') }} border-0 mb-2 py-2 text-dark d-flex align-items-center">
                <i class="bx bxs-circle text-{{ DashboardExecutive::warningDotClass($warning['level'] ?? 'info') }} me-2"></i>
                <span>{{ $warning['message'] }}</span>
            </div>
        @empty
            <p class="text-muted mb-0">Tidak ada peringatan saat ini. Kondisi normal.</p>
        @endforelse
    </div>
</div>
