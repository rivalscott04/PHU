@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header ps-0 d-flex justify-content-between align-items-center">
                    <h6>Data User</h6>
                    <a href="{{ route('form.addUser') }}" class="btn btn-primary">Tambah User</a>
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
                                        style="width: 15%">Role</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                        style="width: 40%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    @if ($user->role === 'user')
                                        <tr class="text-center">
                                            <td class="text-sm font-weight-bold">{{ $loop->iteration }}</td>
                                            <td class="text-sm font-weight-bold">{{ $user->username }}</td>
                                            <td class="text-sm font-weight-bold">{{ $user->email }}</td>
                                            <td class="text-sm font-weight-bold">
                                                @if ($user->role === 'user')
                                                    <span class="badge bg-info">Travel</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $user->role }}</span>
                                                @endif
                                            </td>
                                            <td class="text-sm font-weight-bold">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <a href="{{ route('impersonate.take', $user->id) }}" 
                                                       class="btn btn-success btn-sm waves-effect waves-light"
                                                       onclick="return confirmImpersonate(event, '{{ $user->username }}')"
                                                       title="Impersonate User">
                                                        <i class="bx bx-user-check me-1"></i>
                                                        Impersonate
                                                    </a>
                                                    <button type="button" class="btn btn-danger btn-sm waves-effect waves-light"
                                                        onclick="confirmResetPassword({{ $user->id }})" title="Reset Password">
                                                        <i class="bx bx-refresh me-1"></i>
                                                        Reset Password
                                                    </button>
                                                </div>
                                                <form id="reset-password-form-{{ $user->id }}"
                                                    action="{{ route('resetPassword', $user->id) }}" method="POST"
                                                    style="display: none;">
                                                    @csrf
                                                    @method('PUT')
                                                </form>
                                            </td>
                                        </tr>
                                    @endif
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
        function confirmResetPassword(userId) {
            Swal.fire({
                title: "Apakah Anda yakin?",
                text: "Password pengguna akan di-reset ke default!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#34c38f",
                cancelButtonColor: "#f46a6a",
                confirmButtonText: "Ya, reset password!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`reset-password-form-${userId}`).submit();
                }
            });
        }

        function confirmImpersonate(event, username) {
            event.preventDefault(); // Prevent default link behavior
            
            Swal.fire({
                title: "Impersonate User?",
                text: `Anda akan masuk sebagai ${username}. Anda dapat melihat sistem dari perspektif user ini.`,
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#28a745",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Ya, Impersonate!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    // Navigate to impersonate URL
                    window.location.href = event.target.href;
                }
            });
            
            return false; // Prevent default behavior
        }
    </script>
@endpush
