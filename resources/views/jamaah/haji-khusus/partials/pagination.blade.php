@if($jamaahHajiKhusus->hasPages() || $jamaahHajiKhusus->total() > 0)
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 px-3 py-3 border-top">
        <div class="text-muted small" id="hajiKhususResultsInfo">
            Menampilkan {{ $jamaahHajiKhusus->firstItem() ?? 0 }} sampai {{ $jamaahHajiKhusus->lastItem() ?? 0 }}
            dari {{ $jamaahHajiKhusus->total() }} data
        </div>
        @if($jamaahHajiKhusus->hasPages())
            <div id="hajiKhususPaginationLinks">
                {{ $jamaahHajiKhusus->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
@endif
