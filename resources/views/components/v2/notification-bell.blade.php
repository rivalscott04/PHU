@auth
@if(\Illuminate\Support\Facades\Schema::hasTable('notifications'))
@php
    $recentNotifications = auth()->user()->unreadNotifications()->latest()->take(5)->get();
    $unreadCount = auth()->user()->unreadNotifications()->count();
@endphp
<div class="dropdown d-inline-block">
    <button type="button" class="btn header-item noti-icon waves-effect position-relative"
        id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="bx bx-bell font-size-20"></i>
        @if($unreadCount > 0)
            <span class="badge bg-danger rounded-pill position-absolute top-0 end-0" style="font-size:10px;">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" style="min-width:320px;"
        aria-labelledby="page-header-notifications-dropdown">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="m-0">Notifikasi</h6>
            @if($unreadCount > 0)
                <form method="POST" action="{{ route('v2.notifications.read-all') }}" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-link btn-sm p-0">Tandai semua</button>
                </form>
            @endif
        </div>
        <div data-simplebar style="max-height:280px;">
            @forelse ($recentNotifications as $notification)
                @php $data = $notification->data; @endphp
                <a href="{{ $data['url'] ?? route('v2.notifications.index') }}" class="dropdown-item notify-item py-2 border-bottom">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fs-13">{{ $data['title'] ?? 'Notifikasi' }}</h6>
                            <p class="mb-0 text-muted fs-12">{{ \Illuminate\Support\Str::limit($data['message'] ?? '', 80) }}</p>
                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                </a>
            @empty
                <div class="dropdown-item text-center text-muted py-4">
                    Tidak ada notifikasi baru.
                </div>
            @endforelse
        </div>
        <div class="p-2 border-top text-center">
            <a href="{{ route('v2.notifications.index') }}" class="btn btn-sm btn-light w-100">Lihat Semua</a>
        </div>
    </div>
</div>
@endif
@endauth
