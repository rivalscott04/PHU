@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Master Checklist</h4>
            @can('create', \App\Models\Checklist::class)
                <a href="{{ route('v2.checklist.create') }}" class="btn btn-primary btn-sm">Tambah Checklist</a>
            @endcan
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead><tr><th>Kode</th><th>Judul</th><th>Kategori</th><th>Jenis</th><th>Bobot</th><th>Status</th><th>Aksi</th></tr></thead>
                    <tbody>
                        @foreach ($checklists as $checklist)
                            <tr>
                                <td>{{ $checklist->code }}</td>
                                <td>{{ $checklist->title }}</td>
                                <td>{{ $checklist->category?->name }}</td>
                                <td>{{ $checklist->input_type?->label() ?? '-' }}</td>
                                <td>{{ $checklist->weight }}</td>
                                <td>{{ $checklist->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                                <td>
                                    @can('update', $checklist)
                                        <a href="{{ route('v2.checklist.edit', $checklist) }}" class="btn btn-sm btn-outline-warning">Edit</a>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $checklists->links() }}
        </div>
    </div>
</div>
@endsection
