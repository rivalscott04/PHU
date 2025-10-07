@forelse ($travelUsers as $user)
    <tr class="text-center">
        <td class="text-sm font-weight-bold">{{ $travelUsers->firstItem() + $loop->index }}</td>
        <td class="text-sm font-weight-bold">{{ $user->nama }}</td>
        <td class="text-sm font-weight-bold">{{ $user->email }}</td>
        <td class="text-sm font-weight-bold">{{ $user->nomor_hp }}</td>
        <td class="text-sm font-weight-bold">
            <span class="badge {{ $user->getTravelCompanyBadgeClass() }}">
                {{ $user->getTravelCompanyName() }}
            </span>
        </td>
        @if(auth()->user()->role === 'admin')
        <td class="text-sm font-weight-bold">
            <span class="badge bg-success">{{ $user->getKabupaten() }}</span>
        </td>
        @endif
        <td class="text-sm font-weight-bold">
            <div class="d-flex justify-content-center gap-2">
                <a href="{{ route('impersonate.take', $user->id) }}" 
                   class="btn btn-success btn-sm waves-effect waves-light"
                   onclick="return confirmImpersonate(event, '{{ $user->nama }}')"
                   title="Impersonate User">
                    <i class="bx bx-user-check me-1"></i>
                    Impersonate
                </a>
                <a href="{{ route('users.edit', $user->id) }}" 
                   class="btn btn-warning btn-sm waves-effect waves-light"
                   title="Edit User">
                    <i class="bx bx-edit me-1"></i>
                    Edit
                </a>
                <button type="button" class="btn btn-danger btn-sm waves-effect waves-light"
                    onclick="confirmDelete({{ $user->id }}, '{{ $user->nama }}')" 
                    title="Delete User">
                    <i class="bx bx-trash me-1"></i>
                    Delete
                </button>
            </div>
            <form id="delete-form-{{ $user->id }}"
                action="{{ route('users.destroy', $user->id) }}" method="POST"
                style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="{{ auth()->user()->role === 'admin' ? '7' : '6' }}" class="text-center">
            <div class="empty-state text-muted">
                <i class="bx bx-search-alt-2"></i>
                <p class="mt-2 mb-0">Tidak ada data ditemukan</p>
                <small>Silakan coba dengan kriteria pencarian yang berbeda</small>
            </div>
        </td>
    </tr>
@endforelse
