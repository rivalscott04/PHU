@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Form Tambah User</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('addUser') }}">
                        @csrf
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="travel_id" class="form-label">Pilih Travel</label>
                                <select class="form-select" id="travel_id" name="travel_id" required>
                                    <option value="">Pilih Travel</option>
                                    @foreach ($travels as $travel)
                                        <option value="{{ $travel->id }}">{{ $travel->Penyelenggara }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="{{ old('email') }}" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="role" class="form-label">Role</label>
                                <input type="text" class="form-control" id="role" name="role" value="user"
                                    readonly>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
