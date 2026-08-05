@php
    use App\Support\DashboardExecutive;
@endphp

<div class="card border-0 shadow-sm mb-3" id="v2-dashboard-warnings">
    <div class="card-header bg-transparent border-bottom">
        <h5 class="mb-0 fw-semibold">Peringatan Dini</h5>
        <small class="text-muted">Isu yang perlu ditindaklanjuti pada periode terpilih</small>
    </div>
    <div class="card-body" id="warning-list">
        @forelse ($warnings as $warning)
            <div class="d-flex align-items-start gap-2 mb-2 pb-2 border-bottom">
                <i class="bx {{ DashboardExecutive::warningIconClass($warning['level'] ?? 'info') }} mt-1"></i>
                <span class="text-body mb-0">{{ $warning['message'] }}</span>
            </div>
        @empty
            <p class="text-muted mb-0">Tidak ada peringatan saat ini. Kondisi normal.</p>
        @endforelse
    </div>
</div>
