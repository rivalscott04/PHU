@php
    $formId = $formId ?? 'public';
    $lockedTravel = $lockedTravel ?? false;
    $travels = $travels ?? collect();
    $sectionId = $sectionId ?? 'pengaduan-travel';
    $inModal = $inModal ?? false;
@endphp

@if(!$inModal)
<section id="{{ $sectionId }}" class="public-pengaduan-form">
    <div class="public-pengaduan-form__head">
        <h2>Ajukan Pengaduan</h2>
        <p>
            @if($lockedTravel && isset($travel))
                Laporkan masalah terkait <strong>{{ $travel->Penyelenggara }}</strong> langsung ke Kanwil NTB.
            @else
                Laporkan masalah terkait layanan travel ke Kanwil NTB.
            @endif
        </p>
    </div>

    @if(session('success'))
        <div class="alert alert-success public-pengaduan-form__alert" role="alert">
            <i class="fas fa-circle-check me-1"></i>{{ session('success') }}
        </div>
    @endif
@endif

@if($errors->any())
    <div class="alert alert-danger public-pengaduan-form__alert" role="alert">
        <strong>Mohon perbaiki data berikut:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form
    id="pengaduanForm-{{ $formId }}"
    class="public-pengaduan-form__body @if($inModal) public-pengaduan-form__body--modal @endif"
    action="{{ route('pengaduan.store-public') }}"
    method="post"
    enctype="multipart/form-data"
    data-form-id="{{ $formId }}"
    @if($inModal) data-in-modal="1" @endif
>
    @csrf

    @if($lockedTravel && isset($travel))
        <input type="hidden" name="_form_context" value="travel-profile">
        <input type="hidden" name="travels_id" value="{{ $travel->id }}">
        <div class="public-pengaduan-form__travel-lock">
            <span class="public-pengaduan-form__travel-lock-label">Travel yang dilaporkan</span>
            <strong>{{ $travel->Penyelenggara }}</strong>
            <span class="text-muted">· {{ $travel->kab_kota }} · {{ $travel->Status }}</span>
        </div>
    @else
        <div class="mb-3">
            <label for="travels_id_{{ $formId }}" class="form-label">Travel</label>
            <select class="form-select" name="travels_id" id="travels_id_{{ $formId }}" required>
                <option value="">-- Pilih Travel --</option>
                @foreach ($travels as $travelOption)
                    <option value="{{ $travelOption->id }}" {{ (string) old('travels_id') === (string) $travelOption->id ? 'selected' : '' }}>
                        {{ $travelOption->Penyelenggara }} ({{ $travelOption->kab_kota }})
                    </option>
                @endforeach
            </select>
        </div>
    @endif

    <div class="row g-3">
        <div class="{{ $inModal ? 'col-12' : 'col-md-6' }}">
            <label for="nama_pengadu_{{ $formId }}" class="form-label">Nama Pengadu</label>
            <input
                type="text"
                name="nama_pengadu"
                id="nama_pengadu_{{ $formId }}"
                class="form-control @error('nama_pengadu') is-invalid @enderror"
                placeholder="Nama lengkap"
                required
                value="{{ old('nama_pengadu') }}"
            />
            @error('nama_pengadu')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="{{ $inModal ? 'col-12' : 'col-md-6' }}">
            <label for="berkas_aduan_{{ $formId }}" class="form-label">Lampiran (opsional)</label>
            <input
                type="file"
                class="form-control @error('berkas_aduan') is-invalid @enderror"
                name="berkas_aduan"
                id="berkas_aduan_{{ $formId }}"
                accept=".pdf,.jpg,.jpeg,.png"
            />
            <div class="form-text">Maks. 2MB. PDF, JPG, atau PNG.</div>
            @error('berkas_aduan')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-12">
            <label for="hal_aduan_{{ $formId }}" class="form-label">Hal yang Diadukan</label>
            <textarea
                class="form-control @error('hal_aduan') is-invalid @enderror"
                name="hal_aduan"
                id="hal_aduan_{{ $formId }}"
                rows="{{ $inModal ? 4 : 5 }}"
                placeholder="Jelaskan masalah yang Anda alami..."
                required
            >{{ old('hal_aduan') }}</textarea>
            @error('hal_aduan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        @if($inModal)
            <div class="col-12 public-pengaduan-form__modal-actions">
                <button type="button" class="btn btn-outline-secondary btn-profile-cta" data-bs-dismiss="modal">
                    Batal
                </button>
                <button type="submit" class="btn btn-primary btn-profile-cta">
                    <i class="fas fa-paper-plane me-1"></i>Kirim Pengaduan
                </button>
            </div>
        @else
            <div class="col-12">
                <button type="submit" class="btn btn-primary btn-profile-cta">
                    <i class="fas fa-paper-plane me-1"></i>Kirim Pengaduan
                </button>
            </div>
        @endif
    </div>
</form>

@if(!$inModal)
</section>
@endif
