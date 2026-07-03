<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 px-3 py-3 border-top">
    <div class="text-muted">
        Menampilkan {{ $users->firstItem() ?? 0 }} sampai {{ $users->lastItem() ?? 0 }}
        dari {{ $users->total() }} data
    </div>
    <div>
        {{ $users->onEachSide(1)->links('pagination::bootstrap-4') }}
    </div>
</div>
