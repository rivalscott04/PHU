{{--
    Penanda berkas yang masih tersimpan dari percobaan kirim sebelumnya.
    Butuh $field, dan $berkasTersimpan dari controller (kosong di halaman lain).
--}}
@php $namaBerkasTersimpan = ($berkasTersimpan ?? [])[$field] ?? null; @endphp

@if ($namaBerkasTersimpan)
    <div class="form-text text-success">
        <i class="bx bx-check-circle me-1"></i>
        Berkas yang tadi Anda unggah masih tersimpan: <strong>{{ $namaBerkasTersimpan }}</strong>.
        Biarkan kosong bila tidak diganti.
    </div>
@endif
