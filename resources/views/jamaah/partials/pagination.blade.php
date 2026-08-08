@if($jamaah->hasPages() || $jamaah->total() > 0)
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 px-3 py-3 border-top">
        <div class="text-muted small" id="jamaahResultsInfo">
            Menampilkan {{ $jamaah->firstItem() ?? 0 }} sampai {{ $jamaah->lastItem() ?? 0 }}
            dari {{ $jamaah->total() }} data
        </div>
        @if($jamaah->hasPages())
            <div id="jamaahPaginationLinks">
                {{ $jamaah->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
@endif
