@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Data User</h5>
                    <a href="{{ route('form.addUser') }}" class="btn btn-primary btn-sm">
                        <i class="bx bx-plus me-1"></i> Tambah User
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered mb-0">
                            <thead class="table-light text-center">
                                <tr>
                                    <th>No.</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Nomor HP</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    @if ($user->role === 'user')
                                        <tr class="text-center align-middle">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $user->nama }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->nomor_hp }}</td>
                                            <td>
                                                <div class="d-flex justify-content-center gap-2">
                                                    <a href="{{ route('impersonate.take', $user->id) }}" 
                                                       class="btn btn-success btn-sm waves-effect waves-light"
                                                       onclick="return confirmImpersonate(event, '{{ $user->nama }}')"
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
    </script>
@endpush
