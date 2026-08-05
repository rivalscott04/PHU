<div class="row">
    <div class="col-md-6 mb-3">
        <label for="dokumen_sk" class="form-label">
            Scan SK / Izin Operasional @include('partials.required-star')
        </label>
        <input type="file" class="form-control @error('dokumen_sk') is-invalid @enderror"
            id="dokumen_sk" name="dokumen_sk" accept=".pdf,.jpg,.jpeg,.png" required>
        @error('dokumen_sk')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <small class="text-muted">PDF atau foto (JPG/PNG), maks. 1,5 MB</small>
    </div>

    <div class="col-md-6 mb-3">
        <label for="dokumen_akreditasi" class="form-label">
            Scan Sertifikat Akreditasi @include('partials.required-star')
        </label>
        <input type="file" class="form-control @error('dokumen_akreditasi') is-invalid @enderror"
            id="dokumen_akreditasi" name="dokumen_akreditasi" accept=".pdf,.jpg,.jpeg,.png" required>
        @error('dokumen_akreditasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <small class="text-muted">PDF atau foto (JPG/PNG), maks. 1,5 MB</small>
    </div>
</div>
