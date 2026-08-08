@if($data->hasPages() || $data->total() > 0)
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 px-3 py-2 border-top">
        <div class="text-muted small" id="bapResultsInfo">
            Menampilkan {{ $data->firstItem() ?? 0 }} sampai {{ $data->lastItem() ?? 0 }}
            dari {{ $data->total() }} data
        </div>
        @if($data->hasPages())
            <div id="bapPaginationLinks">
                {{ $data->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
@endif
