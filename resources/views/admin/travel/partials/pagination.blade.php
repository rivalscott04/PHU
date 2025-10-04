@if($travelUsers->hasPages())
<div class="d-flex justify-content-between align-items-center px-3 py-3">
    <div class="text-muted">
        Menampilkan {{ $travelUsers->firstItem() ?? 0 }} - {{ $travelUsers->lastItem() ?? 0 }} 
        dari {{ $travelUsers->total() }} data
    </div>
    <div>
        {{ $travelUsers->links('pagination::bootstrap-4') }}
    </div>
</div>
@endif
