<div class="card mb-3" id="v2-dashboard-warnings">
    <div class="card-header bg-transparent">
        <h5 class="mb-0 text-danger"><i class="bx bx-error-circle me-1"></i> Early Warning</h5>
    </div>
    <div class="card-body" id="warning-list">
        @forelse ($warnings as $warning)
            <div class="alert alert-{{ $warning['level'] === 'critical' ? 'danger' : ($warning['level'] === 'warning' ? 'warning' : 'info') }} mb-2 py-2">
                <span class="me-2">{{ $warning['icon'] }}</span>{{ $warning['message'] }}
            </div>
        @empty
            <p class="text-muted mb-0">Tidak ada peringatan saat ini. Kondisi normal.</p>
        @endforelse
    </div>
</div>
