@forelse ($data as $item)
    <tr class="text-center">
        <td>{{ $data->firstItem() + $loop->index }}</td>
        <td>{{ $item->name }}</td>
        <td>{{ $item->jabatan }}</td>
        <td>{{ $item->ppiuname }}</td>
        <td>{{ $item->address_phone }}</td>
        <td>{{ $item->kab_kota }}</td>
        <td>{{ \Carbon\Carbon::parse($item->datetime)->format('d/m/Y') }}</td>
        <td>{{ $item->people }}</td>
        <td><span>Rp. </span>{{ number_format($item->price, 2, ',', '.') }}</td>
        <td>
            @if (auth()->user()->role === 'admin' || auth()->user()->role === 'kabupaten')
                <form action="{{ route('bap.updateStatus', $item->id) }}" method="POST" id="statusForm{{ $item->id }}">
                    @csrf
                    <div class="d-flex flex-column gap-1">
                        <select name="status"
                            class="form-select form-select-sm {{ $item->status == 'diajukan' ? 'bg-primary text-white fw-semibold' : '' }}
                                {{ $item->status == 'diproses' ? 'bg-warning text-dark fw-semibold' : '' }}
                                {{ $item->status == 'diterima' ? 'bg-success text-white fw-semibold' : '' }}"
                            onchange="handleStatusChange({{ $item->id }}, this.value)">
                            @foreach (\App\Enums\BapStatus::cases() as $bapStatus)
                                <option value="{{ $bapStatus->value }}" {{ $item->status == $bapStatus->value ? 'selected' : '' }}>
                                    {{ $bapStatus->label() }}
                                </option>
                            @endforeach
                        </select>
                        @if ($item->status === 'diterima' && $item->nomor_surat)
                            <small class="text-muted">{{ $item->nomor_surat }}</small>
                        @endif
                    </div>
                </form>
            @else
                @php $badge = \App\Support\BapWizardStatus::travelBadge($item); @endphp
                <div>
                    <span class="badge {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                    @if ($item->status === 'diterima' && $item->nomor_surat)
                        <small class="text-muted d-block mt-1">{{ $item->nomor_surat }}</small>
                    @endif
                </div>
            @endif
        </td>
        <td>
            <div class="d-flex gap-2 justify-content-center align-items-center">
                @if (auth()->user()->role === 'user' && ($wizardRoute = \App\Support\BapWizardStatus::wizardRouteName($item)))
                    <a href="{{ route($wizardRoute, $item->id) }}" class="btn btn-sm btn-warning" title="Lanjutkan pengajuan">
                        Lanjutkan
                    </a>
                @endif
                <a href="{{ route('detail.bap', $item->id) }}" class="btn btn-sm btn-outline-primary" title="Detail">
                    <i class="bx bx-info-circle"></i>
                </a>
                @if ($item->status === 'diterima')
                    <a href="{{ route('cetak.bap', $item->id) }}" target="_blank" class="btn btn-sm btn-outline-success" title="Cetak BAP">
                        <i class="bx bx-printer"></i>
                    </a>
                @endif
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="11" class="text-center py-4 text-muted">
            <i class="bx bx-search-alt-2 fs-4 d-block mb-2"></i>
            Tidak ada pengajuan BA ditemukan
        </td>
    </tr>
@endforelse
