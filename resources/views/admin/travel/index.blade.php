@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header ps-0 d-flex justify-content-between align-items-center">
                    <h6>Data User Travel</h6>
                    <div class="d-flex gap-2">
                        <a href="{{ route('travels.import.form') }}" class="btn btn-success">
                            <i class="bx bx-upload me-1"></i>
                            Import Excel
                        </a>
                        <a href="{{ route('travels.create') }}" class="btn btn-primary">
                            <i class="bx bx-plus me-1"></i>
                            Tambah User Travel
                        </a>
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr class="text-center">
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                        style="width: 5%">No.</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                        style="width: 20%">Nama</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                        style="width: 20%">Email</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                        style="width: 15%">Nomor HP</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                        style="width: 15%">Travel Company</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                        style="width: 25%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($travelUsers as $user)
                                    <tr class="text-center">
                                        <td class="text-sm font-weight-bold">{{ $loop->iteration }}</td>
                                        <td class="text-sm font-weight-bold">{{ $user->nama }}</td>
                                        <td class="text-sm font-weight-bold">{{ $user->email }}</td>
                                        <td class="text-sm font-weight-bold">{{ $user->nomor_hp }}</td>
                                        <td class="text-sm font-weight-bold">
                                            @if($user->travel)
                                                <span class="badge bg-info">{{ $user->travel->Penyelenggara }}</span>
                                            @else
                                                <span class="badge bg-secondary">Tidak ada travel</span>
                                            @endif
                                        </td>
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
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmImpersonate(event, username) {
            event.preventDefault(); // Prevent default link behavior
            
            Swal.fire({
                title: "Impersonate User?",
                text: `Anda akan masuk sebagai ${username}. Anda dapat melihat sistem dari perspektif user ini.`,
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#34c38f",
                cancelButtonColor: "#f46a6a",
                confirmButtonText: "Ya, impersonate!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    // Navigate to impersonate URL
                    window.location.href = event.target.href;
                }
            });
            
            return false; // Prevent default behavior
        }

        function confirmDelete(userId, username) {
            Swal.fire({
                title: "Apakah Anda yakin?",
                text: `User ${username} akan dihapus secara permanen!`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#f46a6a",
                cancelButtonColor: "#34c38f",
                confirmButtonText: "Ya, hapus!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-form-${userId}`).submit();
                }
            });
        }

        @if(session('success'))
            Swal.fire({
                title: "Berhasil!",
                text: "{{ session('success') }}",
                icon: "success",
                confirmButtonColor: "#34c38f"
            });
        @endif

        @if(session('error'))
            Swal.fire({
                title: "Error!",
                text: "{{ session('error') }}",
                icon: "error",
                confirmButtonColor: "#f46a6a"
            });
        @endif
    </script>
@endpush
