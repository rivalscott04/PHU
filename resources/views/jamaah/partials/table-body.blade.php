@forelse ($jamaah as $item)
    <tr class="text-center">
        <td class="text-sm font-weight-bold">{{ $jamaah->firstItem() + $loop->index }}</td>
        <td class="text-sm font-weight-bold">{{ $item->nama }}</td>
        @if($showTravelColumn ?? false)
            <td class="text-sm">
                <span class="badge bg-light text-dark">{{ $item->travel->Penyelenggara ?? '-' }}</span>
                @if($item->travel?->kab_kota)
                    <small class="d-block text-muted mt-1">{{ $item->travel->kab_kota }}</small>
                @endif
            </td>
        @endif
        <td class="text-sm font-weight-bold">{{ $item->alamat }}</td>
        <td class="text-sm font-weight-bold">{{ $item->nomor_hp }}</td>
        <td class="text-sm font-weight-bold" style="width: 200px; min-width: 200px;">
            <div class="d-flex align-items-center justify-content-center">
                <span id="nik_{{ $item->id }}" data-nik="{{ $item->nik }}">{{ str_repeat('*', strlen($item->nik)) }}</span>
                <button type="button" class="btn btn-link p-0 ms-2" onclick="toggleJamaahNik('{{ $item->id }}')" title="Tampilkan/Sembunyikan NIK">
                    <i id="icon_{{ $item->id }}" class="bx bxs-show"></i>
                </button>
            </div>
        </td>
        <td>
            <div class="d-flex justify-content-center gap-1">
                <a href="{{ route('jamaah.detail', $item->id) }}" class="btn btn-sm btn-outline-info" title="Detail">
                    <i class="bx bx-info-circle"></i>
                </a>
                <a href="{{ route('jamaah.edit', $item->id) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                    <i class="bx bx-edit"></i>
                </a>
                <button type="button" class="btn btn-sm btn-outline-danger"
                    onclick="confirmDelete('{{ $item->id }}', '{{ addslashes($item->nama) }}', 'data jamaah')" title="Hapus">
                    <i class="bx bx-trash"></i>
                </button>
                <form id="delete-form-{{ $item->id }}" action="{{ route('jamaah.destroy', $item->id) }}" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="{{ ($showTravelColumn ?? false) ? 7 : 6 }}" class="text-center py-4">
            <div class="text-muted">
                <i class="bx bx-search-alt-2 fs-4 d-block mb-2"></i>
                <p class="mb-0">Tidak ada data jamaah ditemukan</p>
                <small>Coba kata kunci pencarian yang berbeda</small>
            </div>
        </td>
    </tr>
@endforelse
