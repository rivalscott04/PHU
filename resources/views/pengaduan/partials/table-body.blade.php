@forelse ($pengaduan as $item)
    <tr class="text-center">
        <td>{{ $pengaduan->firstItem() + $loop->index }}</td>
        <td>{{ $item->travel->Penyelenggara }}</td>
        <td class="text-start" style="max-width: 300px;">{{ $item->hal_aduan }}</td>
        <td>
            @if ($item->berkas_aduan)
                <a href="{{ route('pengaduan.download-berkas', $item->id) }}" target="_blank" rel="noopener noreferrer">
                    <i class="bx bx-file"></i>
                </a>
            @else
                -
            @endif
        </td>
        <td>
            <select class="form-select form-select-sm status-dropdown"
                data-id="{{ $item->id }}"
                data-current-status="{{ $item->status }}"
                style="width: auto; min-width: 140px;">
                <option value="pending" {{ $item->status == 'pending' ? 'selected' : '' }}>Menunggu</option>
                <option value="in_progress" {{ $item->status == 'in_progress' ? 'selected' : '' }}>Sedang Diproses</option>
                <option value="completed" {{ $item->status == 'completed' ? 'selected' : '' }}>Selesai</option>
            </select>
        </td>
        <td>{{ $item->created_at->format('d/m/Y') }}</td>
        <td>
            <div class="d-flex gap-1 justify-content-center flex-wrap">
                <a href="{{ route('pengaduan.show', $item->id) }}" class="btn btn-sm btn-primary">
                    <i class="bx bx-info-circle me-1"></i> Detail
                </a>
                @if($item->status === 'completed' && $item->pdf_output)
                    <a href="{{ $item->getPublicDownloadUrl() }}" class="btn btn-sm btn-success" target="_blank">
                        <i class="bx bx-download me-1"></i> PDF
                    </a>
                @endif
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="text-center py-4 text-muted">Tidak ada pengaduan ditemukan</td>
    </tr>
@endforelse
