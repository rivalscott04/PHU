@auth
@if(\Illuminate\Support\Facades\Schema::hasTable('notifications'))
@php
    $recentNotifications = auth()->user()->unreadNotifications()->latest()->take(5)->get();
    $unreadCount = auth()->user()->unreadNotifications()->count();
    $reverbEnabled = config('broadcasting.default') === 'reverb' && config('broadcasting.connections.reverb.key');
@endphp
<div class="dropdown d-inline-block">
    <button type="button" class="btn header-item noti-icon waves-effect position-relative"
        id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="bx bx-bell font-size-20"></i>
        @if($unreadCount > 0)
            <span class="badge bg-danger rounded-pill position-absolute end-0 notification-unread-badge"
                style="font-size:10px; top:6px;" data-count="{{ $unreadCount }}">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0" style="min-width:320px;"
        aria-labelledby="page-header-notifications-dropdown">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="m-0">Notifikasi</h6>
            <form method="POST" action="{{ route('v2.notifications.read-all') }}"
                class="m-0 {{ $unreadCount > 0 ? '' : 'd-none' }}" id="notification-mark-all-form">
                @csrf
                <button type="submit" class="btn btn-link btn-sm p-0">Tandai semua</button>
            </form>
        </div>
        <div data-simplebar style="max-height:280px;" id="notification-dropdown-list">
            @forelse ($recentNotifications as $notification)
                @php
                    $data = $notification->data;
                    $url = \App\Support\NotificationUrl::normalize($data['url'] ?? null)
                        ?? route('v2.notifications.index');
                @endphp
                <a href="{{ $url }}" class="dropdown-item notify-item py-2 border-bottom notification-static-item">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fs-13">{{ $data['title'] ?? 'Notifikasi' }}</h6>
                            <p class="mb-0 text-muted fs-12">{{ \Illuminate\Support\Str::limit($data['message'] ?? '', 80) }}</p>
                            <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                </a>
            @empty
                <div class="dropdown-item text-center text-muted py-4 notification-empty-state">
                    Tidak ada notifikasi baru.
                </div>
            @endforelse
        </div>
        <div class="p-2 border-top text-center">
            <a href="{{ route('v2.notifications.index') }}" class="btn btn-sm btn-light w-100">Lihat Semua</a>
        </div>
    </div>
</div>

@if($reverbEnabled)
@push('js')
<script src="https://cdn.jsdelivr.net/npm/pusher-js@8.4.0/dist/web/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.19.0/dist/echo.iife.js"></script>
<script>
window.__PHU_REVERB__ = {
    userId: {{ auth()->id() }},
    indexUrl: @json(route('v2.notifications.index')),
    key: @json(config('broadcasting.connections.reverb.key')),
    host: @json(config('broadcasting.connections.reverb.options.host')),
    port: {{ (int) config('broadcasting.connections.reverb.options.port', 8080) }},
    scheme: @json(config('broadcasting.connections.reverb.options.scheme', 'http')),
    authEndpoint: @json(url('/broadcasting/auth')),
    csrfToken: @json(csrf_token()),
};
window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: 'reverb',
    key: window.__PHU_REVERB__.key,
    wsHost: window.__PHU_REVERB__.host,
    wsPort: window.__PHU_REVERB__.port,
    wssPort: window.__PHU_REVERB__.port,
    forceTLS: window.__PHU_REVERB__.scheme === 'https',
    enabledTransports: ['ws', 'wss'],
    authEndpoint: window.__PHU_REVERB__.authEndpoint,
    auth: {
        headers: {
            'X-CSRF-TOKEN': window.__PHU_REVERB__.csrfToken,
        },
    },
});
</script>
<script src="{{ asset('js/notifications-realtime.js') }}?v={{ filemtime(public_path('js/notifications-realtime.js')) }}"></script>
@endpush
@endif
@endif
@endauth
