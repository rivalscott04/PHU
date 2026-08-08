@forelse ($data as $item)
    <tr class="text-center">
        <td>{{ $data->firstItem() + $loop->index }}</td>
        <td>{{ $item->Penyelenggara }}</td>
        <td>{{ $item->kabupaten }}</td>
        <td>{{ $item->pusat }}</td>
        <td>{{ $item->pimpinan_pusat }}</td>
        <td>{{ $item->alamat_pusat }}</td>
        <td style="min-width: 150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $item->SK_BA }}</td>
        <td>{{ $item->tanggal ? $item->tanggal->format('Y-m-d') : '-' }}</td>
        <td>{{ $item->pimpinan_cabang }}</td>
        <td>{{ $item->alamat_cabang }}</td>
        <td>{{ $item->telepon }}</td>
        <td>
            <div class="btn-group" role="group">
                <a href="{{ route('cabang.travel.edit', $item->id_cabang) }}" class="btn btn-sm btn-warning" title="Edit">
                    <i class="bx bx-edit"></i>
                </a>
                <form id="delete-form-{{ $item->id_cabang }}"
                    action="{{ route('cabang.travel.destroy', $item->id_cabang) }}"
                    method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-sm btn-danger"
                        onclick="confirmDelete('{{ $item->id_cabang }}', '{{ addslashes($item->pimpinan_cabang) }}', 'cabang travel')" title="Hapus">
                        <i class="bx bx-trash"></i>
                    </button>
                </form>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="12" class="text-center py-4 text-muted">
            <i class="bx bx-search-alt-2 fs-4 d-block mb-2"></i>
            Tidak ada data cabang travel ditemukan
        </td>
    </tr>
@endforelse
