@php
    $canVerifyBukti = in_array(auth()->user()->role, ['admin', 'kabupaten'], true);
    $canAssignSpph = $canVerifyBukti;
    $canUpdateStatus = auth()->user()->role === 'admin';
@endphp

@forelse ($jamaahHajiKhusus as $jamaah)
    <tr>
        <td>{{ $jamaahHajiKhusus->firstItem() + $loop->index }}</td>
        @if($showTravelColumn ?? false)
            <td>
                <span class="badge badge-ppiu">{{ $jamaah->travel->Penyelenggara ?? '-' }}</span>
                @if($jamaah->travel?->kab_kota)
                    <small class="d-block text-muted mt-1">{{ $jamaah->travel->kab_kota }}</small>
                @endif
            </td>
        @endif
        <td>
            <div>
                <h6 class="mb-0">{{ $jamaah->nama_lengkap }}</h6>
                <small class="text-muted">{{ $jamaah->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</small>
            </div>
        </td>
        <td><code>{{ $jamaah->no_ktp }}</code></td>
        <td>
            <span class="badge bg-primary">{{ \Carbon\Carbon::parse($jamaah->tanggal_lahir)->age }} tahun</span>
        </td>
        <td><code>{{ $jamaah->no_paspor ?? '-' }}</code></td>
        <td><code>{{ $jamaah->nomor_porsi ?? '-' }}</code></td>
        <td>
            @include('jamaah.haji-khusus.partials.status-pendaftaran-cell', [
                'jamaah' => $jamaah,
                'canUpdateStatus' => $canUpdateStatus,
            ])
        </td>
        <td>
            @include('jamaah.haji-khusus.partials.bukti-setor-cell', [
                'jamaah' => $jamaah,
                'canVerifyBukti' => $canVerifyBukti,
                'canAssignSpph' => $canAssignSpph,
            ])
        </td>
        <td>
            <div class="btn-group" role="group">
                <a href="{{ route('jamaah.haji-khusus.show', $jamaah->id) }}" class="btn btn-sm btn-outline-info" title="Detail">
                    <i class="bx bx-info-circle"></i>
                </a>
                <a href="{{ route('jamaah.haji-khusus.edit', $jamaah->id) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                    <i class="bx bx-edit"></i>
                </a>
                <button type="button" class="btn btn-sm btn-outline-danger"
                    onclick="confirmDelete('{{ $jamaah->id }}', '{{ addslashes($jamaah->nama_lengkap) }}', 'data jamaah')" title="Hapus">
                    <i class="bx bx-trash"></i>
                </button>
            </div>
            <form id="delete-form-{{ $jamaah->id }}" action="{{ route('jamaah.haji-khusus.destroy', $jamaah->id) }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="{{ ($showTravelColumn ?? false) ? 10 : 9 }}" class="text-center py-4 text-muted">
            Tidak ada data jamaah haji khusus ditemukan
        </td>
    </tr>
@endforelse
