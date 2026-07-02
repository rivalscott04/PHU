@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h4 class="mb-0">Notifikasi</h4>
            @if(auth()->user()->unreadNotifications()->count() > 0)
                <form method="POST" action="{{ route('v2.notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-primary">Tandai Semua Dibaca</button>
                </form>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                @forelse ($notifications as $notification)
                    @php $data = $notification->data; @endphp
                    <div class="list-group-item {{ $notification->read_at ? '' : 'bg-light' }}">
                        <div class="d-flex justify-content-between align-items-start gap-3">
                            <div>
                                <h6 class="mb-1">{{ $data['title'] ?? 'Notifikasi' }}</h6>
                                <p class="mb-1 text-muted">{{ $data['message'] ?? '' }}</p>
                                <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                            <div class="d-flex flex-column gap-1">
                                @if(!empty($data['url']))
                                    <a href="{{ $data['url'] }}" class="btn btn-sm btn-outline-primary">Buka</a>
                                @endif
                                @unless($notification->read_at)
                                    <form method="POST" action="{{ route('v2.notifications.read') }}">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $notification->id }}">
                                        <button type="submit" class="btn btn-sm btn-link p-0">Tandai dibaca</button>
                                    </form>
                                @endunless
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="list-group-item text-center text-muted py-5">
                        Belum ada notifikasi.
                    </div>
                @endforelse
            </div>
        </div>
        @if($notifications->hasPages())
            <div class="card-footer">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
