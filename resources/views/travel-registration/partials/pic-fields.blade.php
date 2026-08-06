<div class="row">
    <div class="col-12 col-lg-8 mx-auto mb-4">
        <label for="pic_nama" class="form-label">Nama Lengkap PIC @include('partials.required-star')</label>
        <input type="text" class="form-control form-control-lg @error('pic_nama') is-invalid @enderror"
            id="pic_nama" name="pic_nama" value="{{ old('pic_nama') }}"
            placeholder="Nama penanggung jawab" required>
        @error('pic_nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-text">Orang yang akan mengelola akun travel di sistem</div>
    </div>

    <div class="col-12 col-lg-8 mx-auto mb-4">
        <label for="pic_email" class="form-label">Email @include('partials.required-star')</label>
        <input type="email" class="form-control form-control-lg @error('pic_email') is-invalid @enderror"
            id="pic_email" name="pic_email" value="{{ old('pic_email') }}"
            placeholder="email@travel.com" required>
        @error('pic_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-text">Digunakan untuk login ke sistem</div>
    </div>

    <div class="col-12 col-lg-8 mx-auto mb-4">
        <label for="pic_nomor_hp" class="form-label">Nomor HP (WhatsApp) @include('partials.required-star')</label>
        <input type="tel" class="form-control form-control-lg @error('pic_nomor_hp') is-invalid @enderror"
            id="pic_nomor_hp" name="pic_nomor_hp" value="{{ old('pic_nomor_hp') }}"
            inputmode="numeric" pattern="08[0-9]{6,14}" minlength="8" maxlength="16"
            placeholder="081234567890" autocomplete="tel" required>
        @error('pic_nomor_hp')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-text">Angka saja, diawali 08. Bisa juga dipakai untuk login.</div>
    </div>

    <div class="col-12 col-lg-8 mx-auto mb-4">
        <label for="password" class="form-label">Buat Password @include('partials.required-star')</label>
        <input type="password" class="form-control form-control-lg @error('password') is-invalid @enderror"
            id="password" name="password" minlength="8"
            placeholder="Minimal 8 karakter" required>
        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12 col-lg-8 mx-auto mb-4">
        <label for="password_confirmation" class="form-label">Ulangi Password @include('partials.required-star')</label>
        <input type="password" class="form-control form-control-lg"
            id="password_confirmation" name="password_confirmation" minlength="8"
            placeholder="Ketik ulang password" required>
    </div>
</div>
