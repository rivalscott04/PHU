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
                                        style="width: 25%">Email</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                        style="width: 25%">Role</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                        style="width: 25%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    @if ($user->role === 'user')
                                        <tr class="text-center">
                                            <td class="text-sm font-weight-bold">{{ $loop->iteration }}</td>
                                            <td class="text-sm font-weight-bold">{{ $user->username }}</td>
                                            <td class="text-sm font-weight-bold">{{ $user->email }}</td>
                                            <td class="text-sm font-weight-bold">{{ $user->role }}</td>
                                            <td class="text-sm font-weight-bold">
                                                <button type="button" class="btn btn-danger waves-effect waves-light"
                                                    onclick="confirmResetPassword({{ $user->id }})">Reset
                                                    Password</button>
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
    </script>
@endpush
