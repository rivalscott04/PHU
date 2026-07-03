@php
    $isTravelTab = $activeTab === \App\Enums\UserRole::User->value;
    $isPimpinanTab = $activeTab === \App\Enums\UserRole::Pimpinan->value;
    $isPengawasTab = $activeTab === \App\Enums\UserRole::Pengawas->value;
    $colspan = $isTravelTab ? 7 : 6;
@endphp

@forelse ($users as $managedUser)
    <tr class="text-center">
        <td class="text-sm font-weight-bold">{{ $users->firstItem() + $loop->index }}</td>
        <td class="text-sm font-weight-bold">{{ $managedUser->nama }}</td>
        <td class="text-sm font-weight-bold">{{ $managedUser->email }}</td>
        <td class="text-sm font-weight-bold">{{ $managedUser->nomor_hp }}</td>
        @if($isTravelTab)
            <td class="text-sm font-weight-bold">
                <span class="badge {{ $managedUser->getTravelCompanyBadgeClass() }}">
                    {{ $managedUser->getTravelCompanyName() }}
                </span>
            </td>
            <td class="text-sm font-weight-bold">
                <span class="badge bg-success">{{ $managedUser->getKabupaten() }}</span>
            </td>
        @else
            <td class="text-sm font-weight-bold">
                @if($isPimpinanTab)
                    <span class="badge bg-info">Seluruh NTB</span>
                @elseif($isPengawasTab)
                    <span class="badge bg-success">{{ $managedUser->getWilayahKerjaLabel() }}</span>
                @else
                    <span class="badge bg-success">{{ $managedUser->kabupaten ?? 'Tidak ada' }}</span>
                @endif
            </td>
        @endif
        <td class="text-sm font-weight-bold text-nowrap">
            <div class="btn-group" role="group">
                <a href="{{ route('impersonate.take', $managedUser->id) }}"
                   class="btn btn-success btn-sm"
                   onclick="return confirmImpersonate(event, @js($managedUser->nama))"
                   title="Impersonate">
                    <i class="bx bx-user-check"></i>
                </a>
                <a href="{{ route('users.edit', $managedUser->id) }}" class="btn btn-warning btn-sm" title="Edit">
                    <i class="bx bx-edit"></i>
                </a>
                <button type="button" class="btn btn-danger btn-sm"
                    onclick="confirmDelete({{ $managedUser->id }}, '{{ $managedUser->nama }}')"
                    title="Hapus">
                    <i class="bx bx-trash"></i>
                </button>
            </div>
            <form id="delete-form-{{ $managedUser->id }}"
                action="{{ route('users.destroy', $managedUser->id) }}" method="POST" style="display:none;">
                @csrf
                @method('DELETE')
            </form>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="{{ $colspan }}" class="text-center py-4">
            <div class="text-muted">
                <i class="bx bx-search-alt-2 fs-4 d-block mb-2"></i>
                <p class="mb-0">Tidak ada data ditemukan</p>
                <small>Coba ubah kata kunci pencarian atau filter</small>
            </div>
        </td>
    </tr>
@endforelse
