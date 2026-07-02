@if(auth()->user()->role === 'kabupaten')
    <div class="alert alert-info border-0 shadow-sm mb-3 py-2" role="status">
        <div class="d-flex align-items-center gap-2">
            <i class="bx bx-map fs-5"></i>
            <span>
                Menampilkan data wilayah <strong>{{ auth()->user()->getKabupaten() }}</strong>
            </span>
        </div>
    </div>
@endif
