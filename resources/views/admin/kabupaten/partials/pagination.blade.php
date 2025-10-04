@if($kabupatenUsers->hasPages())
<div class="d-flex justify-content-between align-items-center px-3 py-3">
    <div class="text-muted">
        Menampilkan {{ $kabupatenUsers->firstItem() ?? 0 }} - {{ $kabupatenUsers->lastItem() ?? 0 }} 
        dari {{ $kabupatenUsers->total() }} data
    </div>
    <div>
        {{ $kabupatenUsers->links('pagination::bootstrap-4') }}
    </div>
</div>
@endif
