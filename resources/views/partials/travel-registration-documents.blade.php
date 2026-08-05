@php
    $compact = $compact ?? false;
    $colClass = $compact ? 'col-12 col-lg-8 mx-auto' : 'col-md-6';
@endphp

<div class="row travel-form-fields">
    <div class="{{ $colClass }} mb-4">
        <div class="travel-upload-card">
            <div class="travel-upload-card-icon">
                <i class="bx bx-file"></i>
            </div>
            <label for="dokumen_sk" class="form-label fw-semibold mb-2">
                Scan SK / Izin Operasional @include('partials.required-star')
            </label>
            <input type="file" class="form-control @error('dokumen_sk') is-invalid @enderror"
                id="dokumen_sk" name="dokumen_sk" accept=".pdf,.jpg,.jpeg,.png" required>
            @error('dokumen_sk')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div class="form-text mt-2">Unggah PDF atau foto (JPG/PNG), maksimal 1,5 MB</div>
        </div>
    </div>

    <div class="{{ $colClass }} mb-4">
        <div class="travel-upload-card">
            <div class="travel-upload-card-icon">
                <i class="bx bx-certification"></i>
            </div>
            <label for="dokumen_akreditasi" class="form-label fw-semibold mb-2">
                Scan Sertifikat Akreditasi @include('partials.required-star')
            </label>
            <input type="file" class="form-control @error('dokumen_akreditasi') is-invalid @enderror"
                id="dokumen_akreditasi" name="dokumen_akreditasi" accept=".pdf,.jpg,.jpeg,.png" required>
            @error('dokumen_akreditasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div class="form-text mt-2">Unggah PDF atau foto (JPG/PNG), maksimal 1,5 MB</div>
        </div>
    </div>
</div>
