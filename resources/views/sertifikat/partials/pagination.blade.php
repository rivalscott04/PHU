@if($sertifikat->hasPages() || $sertifikat->total() > 0)
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">
        <div class="text-muted small" id="sertifikatResultsInfo">
            Menampilkan {{ $sertifikat->firstItem() ?? 0 }} sampai {{ $sertifikat->lastItem() ?? 0 }}
            dari {{ $sertifikat->total() }} data
        </div>
        @if($sertifikat->hasPages())
            <div id="sertifikatPaginationLinks">
                {{ $sertifikat->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
@endif
