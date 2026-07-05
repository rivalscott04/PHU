@if ($jamaah->isNotEmpty())
    <div class="border rounded p-2 bg-light">
        <div class="small text-muted mb-2">{{ $jamaah->count() }} jamaah terpilih</div>
        <ul class="list-unstyled mb-0 small" style="max-height: {{ $maxHeight ?? '160px' }}; overflow-y: auto;">
            @foreach ($jamaah as $row)
                <li class="py-1 border-bottom">
                    <strong>{{ $row->nama }}</strong>
                    @if ($row->nik)
                        <span class="text-muted">· {{ $row->nik }}</span>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
@else
    <p class="text-muted small mb-0">Belum ada jamaah terpilih.</p>
@endif
