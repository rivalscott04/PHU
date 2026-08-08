@if($pengaduan->hasPages() || $pengaduan->total() > 0)
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 px-3 py-2 border-top">
        <div class="text-muted small" id="pengaduanResultsInfo">
            Menampilkan {{ $pengaduan->firstItem() ?? 0 }} sampai {{ $pengaduan->lastItem() ?? 0 }}
            dari {{ $pengaduan->total() }} data
        </div>
        @if($pengaduan->hasPages())
            <div id="pengaduanPaginationLinks">
                {{ $pengaduan->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
@endif
