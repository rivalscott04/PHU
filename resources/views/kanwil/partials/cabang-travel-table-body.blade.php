@php($isAdmin = auth()->user()?->role === 'admin')
@forelse ($data as $item)
    <tr class="text-center">
        <td>{{ $data->firstItem() + $loop->index }}</td>
        <td>{{ $item->Penyelenggara }}</td>
        <td>{{ $item->kabupaten }}</td>
        <td>
            @if ($item->registration_status)
                <span class="badge {{ $item->registration_status->badgeClass() }}">
                    {{ $item->registration_status->label() }}
                </span>
                @if ($item->isRegistrationRejected() && $item->registration_notes)
                    <div class="small text-muted mt-1">{{ $item->registration_notes }}</div>
                @endif
            @else
                <span class="text-muted">-</span>
            @endif
        </td>
        <td>{{ $item->pusat }}</td>
        <td style="min-width: 150px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $item->SK_BA }}</td>
        <td>{{ $item->tanggal ? $item->tanggal->format('Y-m-d') : '-' }}</td>
        <td>{{ $item->pimpinan_cabang }}</td>
        <td>{{ $item->telepon }}</td>
        <td>
            <div class="btn-group" role="group">
                @if ($item->isRegistrationOpen())
                    {{-- Aksi utama diberi label teks. Tiga tombol ikon tanpa
                         keterangan tidak bisa ditebak petugas yang jarang
                         membuka menu ini. --}}
                    <button type="button" class="btn btn-sm btn-info text-nowrap"
                        data-bs-toggle="modal" data-bs-target="#verifikasiCabang{{ $item->id_cabang }}">
                        <i class="bx bx-check-shield me-1"></i> Verifikasi
                    </button>
                @endif
                <a href="{{ route('cabang.travel.edit', $item->id_cabang) }}" class="btn btn-sm btn-warning" title="Edit">
                    <i class="bx bx-edit"></i>
                </a>
                <form id="delete-form-{{ $item->id_cabang }}"
                    action="{{ route('cabang.travel.destroy', $item->id_cabang) }}"
                    method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-sm btn-danger"
                        onclick="confirmDelete('{{ $item->id_cabang }}', '{{ addslashes($item->pimpinan_cabang) }}', 'cabang travel')" title="Hapus">
                        <i class="bx bx-trash"></i>
                    </button>
                </form>
            </div>

            {{-- Modal harus berada di dalam <td>. Ditaruh langsung di bawah <tr>
                 markupnya tidak sah dan parser browser membongkarnya, sampai form
                 di dalamnya ikut terpotong. --}}
            @if ($item->isRegistrationOpen())
                {{-- ponytail: satu modal per baris yang masih perlu tindakan. Baris seperti ini
                     sedikit; kalau nanti ratusan, ganti satu modal bersama yang diisi lewat data-attribute. --}}
                <div class="modal fade text-start" id="verifikasiCabang{{ $item->id_cabang }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Verifikasi Cabang: {{ $item->Penyelenggara }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        {{-- Alamat sengaja tidak ditampilkan di tabel karena
                             memaksa geser horizontal, tetapi tetap dibutuhkan
                             saat peninjauan, jadi tempatnya di sini. --}}
                        <dl class="row small mb-4">
                            <dt class="col-sm-3 text-muted fw-normal">Kantor pusat</dt>
                            <dd class="col-sm-9 mb-2">
                                {{ $item->pimpinan_pusat }}<br>
                                <span class="text-muted">{{ $item->alamat_pusat }}</span>
                            </dd>
                            <dt class="col-sm-3 text-muted fw-normal">Kantor cabang</dt>
                            <dd class="col-sm-9 mb-0">
                                {{ $item->pimpinan_cabang }}<br>
                                <span class="text-muted">{{ $item->alamat_cabang }}</span>
                            </dd>
                        </dl>

                        <p class="text-muted small">Berkas yang diunggah pendaftar. Klik untuk melihat tanpa meninggalkan halaman ini.</p>
                        <div class="d-flex flex-wrap gap-2 mb-4">
                            @if ($item->skPusatPath())
                                @include('partials.document-preview-button', [
                                    'url' => route('cabang.travel.document', ['id' => $item->id_cabang, 'type' => 'sk_pusat']),
                                    'path' => $item->skPusatPath(),
                                    'label' => 'SK Pusat',
                                ])
                            @endif
                            @foreach (\App\Models\CabangTravel::DOKUMEN_PENDAFTARAN as $type => $meta)
                                @if ($item->{$meta['column']})
                                    @include('partials.document-preview-button', [
                                        'url' => route('cabang.travel.document', ['id' => $item->id_cabang, 'type' => $type]),
                                        'path' => $item->{$meta['column']},
                                        'label' => $meta['label'],
                                    ])
                                @else
                                    <span class="badge bg-danger align-self-center">{{ $meta['label'] }} belum ada</span>
                                @endif
                            @endforeach
                            @if ($item->dokumen_rekomendasi)
                                @include('partials.document-preview-button', [
                                    'url' => route('cabang.travel.document', ['id' => $item->id_cabang, 'type' => 'rekomendasi']),
                                    'path' => $item->dokumen_rekomendasi,
                                    'label' => 'Rekomendasi Kabupaten',
                                ])
                            @endif
                        </div>

                        @if ($item->catatan_rekomendasi)
                            <div class="alert alert-light border mb-4">
                                <div class="small text-muted mb-1">
                                    Catatan peninjauan {{ $item->kabupaten }}
                                    @if ($item->recommended_at)
                                        ({{ $item->recommended_at->format('d/m/Y') }})
                                    @endif
                                </div>
                                {{ $item->catatan_rekomendasi }}
                            </div>
                        @endif

                        @if ($item->isRegistrationPending())
                            <form method="POST" enctype="multipart/form-data"
                                action="{{ route('cabang.travel.recommend', $item->id_cabang) }}">
                                @csrf
                                <h6>Unggah Rekomendasi / BA Laporan Peninjauan</h6>
                                <p class="text-muted small">
                                    Setelah diunggah, pendaftaran diteruskan ke Kanwil untuk keputusan akhir.
                                </p>
                                <div class="mb-3">
                                    <input type="file" class="form-control" name="dokumen_rekomendasi"
                                        accept=".pdf,.jpg,.jpeg,.png" required>
                                    <div class="form-text">PDF atau foto, maksimal 1,5 MB</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Catatan Peninjauan (opsional)</label>
                                    <textarea class="form-control" name="catatan_rekomendasi" rows="2" maxlength="1000"
                                        placeholder="Contoh: kantor sudah ditinjau, alamat sesuai, papan nama terpasang."></textarea>
                                    <div class="form-text">Dibaca Kanwil sebagai bahan keputusan.</div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="bx bx-send me-1"></i> Kirim ke Kanwil
                                </button>
                            </form>
                            <hr>
                        @endif

                        @if ($isAdmin)
                            <form method="POST" action="{{ route('cabang.travel.approve', $item->id_cabang) }}" class="mb-3"
                                onsubmit="event.preventDefault(); confirmApproveCabang(this, @js($item->Penyelenggara), @js($item->isRegistrationPending()));">
                                @csrf
                                <h6>Keputusan Kanwil</h6>
                                @if ($item->isRegistrationPending())
                                    <p class="text-muted small">
                                        Cabang ini belum ditinjau Kabupaten/Kota. Menyetujui sekarang akan melewati tahap rekomendasi.
                                    </p>
                                @endif
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="bx bx-check me-1"></i> Setujui &amp; Selesaikan
                                </button>
                            </form>
                            <hr>
                        @endif

                        <form method="POST" action="{{ route('cabang.travel.reject', $item->id_cabang) }}"
                            onsubmit="event.preventDefault(); confirmRejectCabang(this, @js($item->Penyelenggara));">
                            @csrf
                            <h6>Tolak Pendaftaran</h6>
                            <div class="mb-3">
                                <label class="form-label">Alasan Penolakan @include('partials.required-star')</label>
                                <textarea class="form-control" name="registration_notes" rows="2" maxlength="1000" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="bx bx-x me-1"></i> Tolak
                            </button>
                        </form>
                    </div>
                </div>
            </div>
                </div>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="10" class="text-center py-4 text-muted">
            <i class="bx bx-search-alt-2 fs-4 d-block mb-2"></i>
            Tidak ada data cabang travel ditemukan
        </td>
    </tr>
@endforelse
