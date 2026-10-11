<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>BA Pembukaan Cabang {{ $cabang->Penyelenggara }}</title>
    {{-- Kop dan tata letak sama dengan BA pemberangkatan (travel/printBAP). --}}
    <style>
        @page { size: A4 portrait; margin: 0; }
        * { box-sizing: border-box; }
        html, body { margin: 0; padding: 0; font-family: Arial, sans-serif; }
        .sheet { width: 210mm; min-height: 297mm; margin: 0 auto; padding: 9mm 15mm 8mm; }
        .letterhead { position: relative; min-height: 90px; border-bottom: 2px solid black; padding-bottom: 1.5mm; margin-bottom: 4mm; }
        .logo { position: absolute; top: 0; left: 0; height: 90px; width: auto; object-fit: contain; }
        .header { height: 90px; display: flex; flex-direction: column; justify-content: space-between; text-align: center; font-size: 10.5pt; line-height: 1.05; }
        .header-line { flex: 1; display: flex; align-items: center; justify-content: center; }
        .header-line-title { font-weight: bold; }
        .title { text-align: center; font-weight: bold; font-size: 11pt; margin: 3mm 0 4mm; line-height: 1.35; }
        .title u { display: inline-block; }
        .title span { font-weight: normal; }
        .content { font-size: 10.5pt; line-height: 1.38; text-align: justify; }
        .content p { margin: 0 0 2mm; }
        .fields { margin: 0 0 3mm 5mm; }
        .form-group { display: flex; }
        .label { width: 200px; flex-shrink: 0; }
        .content ol { margin: 0 0 3mm; padding-left: 6mm; }
        .content ol ol { padding-left: 6mm; }
        .signature { width: 45%; margin: 5mm 0 0 auto; font-size: 10.5pt; line-height: 1.38; }
        .signature-space { display: block; height: 22mm; }
        .signature-qr { display: block; width: 24mm; height: 24mm; margin: 1mm 0; }
        .doc-footer { margin-top: 6mm; padding-top: 2mm; border-top: 1px solid #999; font-size: 8.5pt; color: #444; }
        .toolbar { display: flex; justify-content: center; gap: 8px; padding: 12px; position: sticky; top: 0; background: #f1f1f1; border-bottom: 1px solid #ddd; }
        .toolbar a, .toolbar button { font: 600 14px Arial, sans-serif; padding: 8px 14px; border-radius: 6px; border: 1px solid #bbb; background: #fff; color: #222; text-decoration: none; cursor: pointer; }
        .toolbar .primary { background: #198754; border-color: #198754; color: #fff; }

        @media screen {
            body { background: #e5e5e5; }
            .sheet { background: white; box-shadow: 0 0.5mm 2mm rgba(0, 0, 0, 0.3); margin: 5mm auto; }
        }
        @media print {
            .toolbar { display: none; }
            /* Setinggi isi saja; min-height tepat 297mm bisa membuat halaman kosong kedua karena pembulatan. */
            .sheet { min-height: 0; }
        }
    </style>
</head>

<body>
    <nav class="toolbar">
        <a href="{{ route('cabang.travel', ['filter' => 'approved']) }}">Kembali</a>
        <button type="button" onclick="window.print()">Cetak</button>
        <a class="primary" href="{{ route('cabang.travel.berita-acara.download', $cabang->id_cabang) }}">Unduh Word</a>
    </nav>

    <section class="sheet">
        <div class="letterhead">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo">
            <div class="header">
                <div class="header-line header-line-title">{{ \App\Support\KanwilContact::get('letterhead_ministry') }}</div>
                <div class="header-line header-line-title">{{ \App\Support\KanwilContact::get('letterhead_office') }}</div>
                <div class="header-line header-line-title">{{ \App\Support\KanwilContact::get('letterhead_province') }}</div>
                <div class="header-line">{{ \App\Support\KanwilContact::get('address') }} Telp. {{ \App\Support\KanwilContact::get('phone') }}</div>
                <div class="header-line">Email: {{ \App\Support\KanwilContact::get('email') }}</div>
            </div>
        </div>

        <div class="title">
            <u>BERITA ACARA LAPORAN PEMBUKAAN PPIU CABANG</u><br>
            <span>Nomor : {{ $v['nomor'] }}</span>
        </div>

        <div class="content">
            <p>Yang bertanda tangan di bawah ini<br>Dengan hormat, kami sampaikan bahwa :</p>
            <div class="fields">
                <div class="form-group"><span class="label">a. Nama Perusahaan Cabang</span>: {{ $v['nama'] }}</div>
                <div class="form-group"><span class="label">b. Cabang</span>: {{ $v['kabupaten'] }}</div>
                <div class="form-group"><span class="label">c. Nama Pimpinan Cabang</span>: {{ $v['pimpinan'] }}</div>
                <div class="form-group"><span class="label">d. Alamat PPIU Cabang</span>: {{ $v['alamat'] }}</div>
                <div class="form-group"><span class="label">e. No Telp/Hp</span>: {{ $v['telepon'] }}</div>
            </div>

            <p>Menyatakan dengan sesungguhnya bahwa :</p>
            <ol>
                <li>PPIU cabang {{ $v['nama'] }} sudah memenuhi syarat dengan melampirkan dokumen sebagai berikut :
                    <ol type="a">
                        <li>Surat Permohonan Laporan pembukaan cabang yang ditandatangani oleh Kepala Cabang {{ $v['nama'] }}
                            ditujukan kepada Kantor Kementerian Agama {{ $v['kabupaten'] }} dengan nomor ; ......................,
                            tanggal ..................</li>
                        <li>Foto copy SK Pusat PPIU {{ $v['pusat'] }} Nomor {{ $v['sk_pusat'] }} Tahun {{ $v['sk_tahun'] }},
                            tanggal {{ $v['sk_tanggal'] }}</li>
                        <li>Lampiran dari sistem OSS kantor cabang PPIU dengan Nomor Induk Beresiko (NIB) {{ $v['nib'] }},
                            tanggal {{ $v['tanggal_nib'] }}</li>
                        <li>Akte Notaris Pembukaan cabang berdasarkan akte Notaris {{ $v['notaris_akta'] }}, dengan nomor :
                            {{ $v['nomor_akta'] }} Tanggal {{ $v['tanggal_akta'] }}</li>
                        <li>Surat Keterangan Domisili Usaha (SKDU) nomor : {{ $v['nomor_skdu'] }} tanggal {{ $v['tanggal_skdu'] }}
                            yang dikeluarkan oleh {{ $v['penerbit_skdu'] }}</li>
                        <li>Nomor KTP /NIK {{ $v['nik_kepala'] }} dan Nomor NPWP {{ $v['npwp_kepala'] }} Pimpinan Cabang</li>
                    </ol>
                </li>
                <li>Apabila dikemudian hari terdapat perubahan terhadap data atau Pengunduran diri dari kantor cabang PPIU
                    {{ $v['nama'] }} maka agar segera melaporkan kembali ke Kantor kementerian Agama {{ $v['kabupaten'] }}.</li>
                <li>Melaporkan status opersioanal kantor PPIU Setiap tangga; 1 Januari tahun berikut nya akan selalu dilakukan
                    update Berita Acara Pelaporan PPIU Cabang, jika tidak melapor maka dianggap sudah tidak beroperasional kembali.</li>
            </ol>

            <p>Demikian berita acara ini dibuat dengan sebenarnya sebagai dasar pemberian ijin melakukan Operasioanal
                Pembukaan Kantor Cabang Penyelenggara Perjalanan Ibadah Umroh.</p>
        </div>

        <div class="signature">
            {{ $v['kota'] }}, {{ $v['tanggal'] }}<br>
            Kepala,
            @if ($qr)
                <img class="signature-qr" src="{{ $qr }}" alt="QR verifikasi tanda tangan">
            @else
                <span class="signature-space"></span>
            @endif
            <span style="white-space: pre">{{ $v['penandatangan'] }}</span>
        </div>
        <div class="doc-footer">
            Dokumen ini ditandatangani secara elektronik melalui Sistem Pengawasan Haji dan Umrah (PANTAU)
            Kanwil Kementerian Haji dan Umrah Provinsi NTB. Pindai QR untuk memeriksa keasliannya.
        </div>
    </section>
</body>

</html>
