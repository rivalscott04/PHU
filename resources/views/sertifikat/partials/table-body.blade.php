@forelse($sertifikat as $item)
    <tr>
        <td>{{ $sertifikat->firstItem() + $loop->index }}</td>
        <td>{{ $item->nama_ppiu }}</td>
        <td>{{ $item->nama_kepala }}</td>
        <td><small class="text-muted">{{ $item->nomor_surat }}</small></td>
        <td><span class="badge bg-info">{{ $item->jenis }}</span></td>
        <td>
            <span class="badge bg-{{ $item->jenis_lokasi == 'pusat' ? 'primary' : 'warning' }}">
                {{ ucfirst($item->jenis_lokasi) }}
            </span>
        </td>
        <td>
            <span class="badge bg-{{ $item->getStatusColor() }}">{{ $item->getStatusText() }}</span>
        </td>
        <td>
            <div class="btn-group" role="group">
                @if ($item->pdf_path)
                    <a href="{{ route('sertifikat.download', $item->id) }}" class="btn btn-sm btn-success" title="Download PDF">
                        <i class="fas fa-file-pdf"></i>
                    </a>
                    <a href="{{ route('sertifikat.view', $item->id) }}" class="btn btn-sm btn-info" title="Lihat PDF" target="_blank">
                        <i class="fas fa-eye"></i>
                    </a>
                @else
                    <button type="button" class="btn btn-sm btn-primary"
                        onclick="generatePdf('{{ $item->id }}', '{{ addslashes($item->nama_ppiu) }}')" title="Generate PDF">
                        <i class="fas fa-file-pdf"></i>
                    </button>
                @endif
                <a href="{{ route('sertifikat.verifikasi', $item->uuid) }}" class="btn btn-sm btn-warning" title="Verifikasi" target="_blank">
                    <i class="fas fa-qrcode"></i>
                </a>
                <button type="button" class="btn btn-sm btn-danger"
                    onclick="confirmDelete('{{ $item->id }}', '{{ addslashes($item->nama_ppiu) }}', 'sertifikat')" title="Hapus">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <form id="delete-form-{{ $item->id }}" action="{{ route('sertifikat.destroy', $item->id) }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="text-center py-4 text-muted">Tidak ada data sertifikat</td>
    </tr>
@endforelse
