@php($jenis = $jenis ?? 'pusat')

<div class="card mb-3">
    <div class="card-body p-4">
        <label for="jenis_pendaftaran" class="form-label fw-semibold">
            Daftar sebagai @include('partials.required-star')
        </label>
        <select id="jenis_pendaftaran" class="form-select form-select-lg"
            data-current="{{ $jenis }}">
            <option value="pusat" data-url="{{ route('travel.registration.create') }}" @selected($jenis === 'pusat')>
                Kantor Pusat (PPIU / PIHK)
            </option>
            <option value="cabang" data-url="{{ route('cabang.registration.create') }}" @selected($jenis === 'cabang')>
                Kantor Cabang
            </option>
        </select>
        <div class="form-text">
            Pilih dulu, formulir di bawah menyesuaikan pilihan Anda.
            Cabang mengisi data yang berbeda dari pusat.
        </div>
    </div>
</div>

<script>
    document.getElementById('jenis_pendaftaran').addEventListener('change', function () {
        const select = this;
        const url = select.selectedOptions[0].dataset.url;

        if (select.value === select.dataset.current) {
            return;
        }

        // Formulirnya berbeda, jadi pindah halaman. Peringatkan hanya kalau ada
        // isian yang sudah diketik supaya tidak hilang diam-diam.
        const terisi = Array.from(document.querySelectorAll('form input, form select, form textarea'))
            .some((field) => field.type !== 'hidden' && field.value);

        if (!terisi) {
            window.location.href = url;

            return;
        }

        confirmAction({
            title: 'Ganti jenis pendaftaran?',
            text: 'Isian yang sudah diketik akan hilang.',
            icon: 'warning',
            confirmText: 'Ya, ganti',
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            } else {
                select.value = select.dataset.current;
            }
        });
    });
</script>
