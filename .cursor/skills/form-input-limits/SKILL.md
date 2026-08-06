---
name: form-input-limits
description: >-
  Centralized NIK and phone number input limits for PHU forms (frontend + backend).
  Use when adding or editing Blade forms, validation rules, imports, or controllers
  that touch NIK, no_ktp, nomor_hp, no_hp, pic_nomor_hp, or telepon fields.
---

# Form Input Limits (NIK & Nomor HP)

PHU memakai **satu sumber kebenaran** di `ValidationHelper` plus skrip global `input-limits.js`. Jangan hardcode `maxlength="16"` atau regex HP di form/controller baru.

## Konstanta & field name

| Tipe | Konstanta | Panjang | Field `name`/`id` yang dikenali |
|------|-----------|---------|----------------------------------|
| NIK | `ValidationHelper::NIK_LENGTH` (16) | tepat 16 digit | `nik`, `no_ktp` |
| Nomor HP | `ValidationHelper::NOMOR_HP_MAX` (16) | 8–16 digit, diawali `08` | `nomor_hp`, `no_hp`, `pic_nomor_hp`, `telepon`, `Telepon` |

Field baru **wajib** memakai salah satu nama di atas agar otomatis ter-cover JS global. Jika butuh nama berbeda, tambahkan ke `NIK_FIELD_NAMES` / `PHONE_FIELD_NAMES` di `ValidationHelper`, bukan duplikasi logic.

## Form Blade baru

```blade
{{-- NIK --}}
<input type="text" name="nik" value="{{ old('nik') }}"
    {!! \App\Helpers\ValidationHelper::renderInputAttributes(
        \App\Helpers\ValidationHelper::nikInputAttributes(['required' => true])
    ) !!}>

{{-- Nomor HP --}}
<input type="text" name="nomor_hp" value="{{ old('nomor_hp') }}"
    {!! \App\Helpers\ValidationHelper::renderInputAttributes(
        \App\Helpers\ValidationHelper::nomorHpInputAttributes(['required' => true])
    ) !!}>
```

Atribut dari helper sudah mencakup `maxlength`, `inputmode="numeric"`, dan `data-digits-only`.

## Validasi backend (wajib)

```php
use App\Helpers\ValidationHelper;

// Controller / FormRequest
'nik' => ValidationHelper::nikRules(),
'no_ktp' => ValidationHelper::nikRules(),
'nomor_hp' => ValidationHelper::nomorHpRules(),
'no_hp' => ValidationHelper::nomorHpRules(),
'pic_nomor_hp' => ValidationHelper::nomorHpRules(uniqueInUsers: true),
'telepon' => ValidationHelper::teleponRules(),
'Telepon' => ValidationHelper::teleponRules(),
```

Import Excel: pakai helper yang sama (`JamaahImport`, `UserTravelImport` sebagai referensi).

## Frontend global (otomatis)

`public/js/input-limits.js` dibaca via `@include('partials.input-limits-script')`:

- Sudah ada di `layouts/app.blade.php`, `auth/login.blade.php`, `travel-registration/create.blade.php`
- Layout standalone baru **wajib** include partial ini sebelum `@stack('js')`
- JS mendeteksi field by `name`/`id`, hanya angka, potong otomatis saat melebihi batas, termasuk paste

Form lama tanpa helper Blade tetap ter-cover selama `name`/`id` sesuai daftar di atas.

## Pengecualian

- **`email_or_phone` (login)**: boleh email atau HP — **jangan** pakai `nikInputAttributes` / `nomorHpInputAttributes` dan jangan masukkan ke `PHONE_FIELD_NAMES`
- Field **readonly** (mis. NIK saat edit): JS melewati field readonly/disabled

## Checklist form baru

- [ ] `name`/`id` sesuai konvensi (`nik`, `no_ktp`, `nomor_hp`, …)
- [ ] Blade pakai `ValidationHelper::renderInputAttributes(...)` **atau** yakin layout sudah load `input-limits-script`
- [ ] Controller/import pakai `nikRules()` / `nomorHpRules()` / `teleponRules()`
- [ ] Jangan duplikasi `maxlength`, regex HP, atau pesan error manual — gunakan `ValidationHelper::messages()` / `validate()`
- [ ] Layout standalone include `@include('partials.input-limits-script')`

## File referensi

| File | Peran |
|------|-------|
| `app/Helpers/ValidationHelper.php` | Konstanta, rules, atribut HTML, config JS |
| `public/js/input-limits.js` | Pembatasan input client-side |
| `resources/views/partials/input-limits-script.blade.php` | Inject config + load JS |
| `database/migrations/2026_08_06_160000_align_nik_and_phone_column_lengths.php` | Panjang kolom DB |

## Mengubah batas panjang

Ubah **hanya** di `ValidationHelper` (`NIK_LENGTH`, `NOMOR_HP_MAX`, `NOMOR_HP_REGEX`), lalu sesuaikan migration/kolom DB jika perlu. JS mengambil config dari `ValidationHelper::inputLimitConfig()` — tidak perlu edit angka di banyak Blade.
