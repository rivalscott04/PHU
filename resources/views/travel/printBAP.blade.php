<!DOCTYPE html>
<html lang="id" class="no-js">

<head>
    <meta charset="UTF-8">
    <title>BA Pemberangkatan</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        .sheet {
            width: 210mm;
            height: 297mm;
            margin: 0 auto;
            padding: 10mm 12mm 8mm;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .letterhead {
            position: relative;
            min-height: 90px;
            border-bottom: 2px solid black;
            padding-bottom: 1.5mm;
            margin-bottom: 2mm;
            flex-shrink: 0;
        }

        .logo {
            position: absolute;
            top: 0;
            left: 0;
            height: 90px;
            width: auto;
            z-index: 0;
            pointer-events: none;
            object-fit: contain;
        }

        .header {
            position: relative;
            z-index: 1;
            height: 90px;
            width: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-align: center;
            font-size: 10.5pt;
            line-height: 1.05;
        }

        .header-line {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .header-line-title {
            font-weight: bold;
        }

        .title {
            text-align: center;
            font-weight: bold;
            margin: 1.5mm 0 2mm;
            font-size: 10.5pt;
            line-height: 1.15;
            flex-shrink: 0;
        }

        .content {
            flex-shrink: 1;
            font-size: 10pt;
            line-height: 1.12;
        }

        .form-group {
            margin: 0.3mm 0;
            display: flex;
            align-items: center;
            line-height: 1.2;
        }

        .label {
            width: 168px;
            flex-shrink: 0;
        }

        .input-line {
            flex-grow: 1;
            margin: 0 1.5mm;
        }

        .sheet-bottom {
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            flex: 1 1 auto;
            min-height: 0;
        }

        .signatures {
            display: flex;
            justify-content: space-between;
            text-align: center;
            font-size: 10pt;
            line-height: 1.15;
            margin-top: 4mm;
            flex-shrink: 0;
        }

        .signature {
            width: 42%;
        }

        .signature img {
            width: 68px;
            height: 68px;
            margin: 1mm auto 0;
            display: block;
        }

        .signature-space {
            display: inline-block;
            width: 68px;
            height: 68px;
        }

        .doc-footer {
            margin-top: auto;
            border-top: 1px solid #333;
            padding-top: 1.5mm;
            text-align: left;
            flex-shrink: 0;
        }

        .doc-footer__text {
            font-size: 6pt;
            line-height: 1.35;
            color: #222;
        }

        .doc-footer__token {
            margin-top: 0.5mm;
            font-size: 6pt;
            word-break: break-all;
        }

        ol {
            padding-left: 4.5mm;
            margin: 4mm 0;
        }

        li {
            text-align: justify;
            margin-bottom: 4mm;
            line-height: 1.2;
        }

        li:last-child {
            margin-bottom: 0;
        }

        p {
            text-align: justify;
            margin: 0 0 4mm;
            line-height: 1.2;
        }

        p.after-fields {
            margin-top: 6mm;
        }

        .content > p:last-child {
            margin-bottom: 0;
        }

        @media print {
            html,
            body {
                width: 210mm;
                height: 297mm;
                overflow: hidden;
            }

            .sheet {
                margin: 0;
                page-break-after: avoid;
                page-break-inside: avoid;
                break-inside: avoid;
            }

            img {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }

        @media screen {
            body {
                background: #e0e0e0;
            }

            .sheet {
                background: white;
                box-shadow: 0 0.5mm 2mm rgba(0, 0, 0, 0.3);
                margin: 5mm auto;
            }
        }
    </style>
</head>

<body>
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
            BERITA ACARA<br>
            PELAPORAN PEMBERANGKATAN JAMAAH UMROH<br>
            Nomor : {{ $data->nomor_surat ?? 'B-xxx/Kw.18.04/2/Hj.00/xx/xxxx' }}
        </div>

        <div class="content">
            <p><b>{{ app('App\Http\Controllers\BAPController')->tanggalDalamFormatBaru(now()) }}</b>,
                yang bertanda tangan dibawah ini :</p>

            <div class="form-group">
                <span class="label">Nama</span>: <span class="input-line">{{ $data->name }}</span>
            </div>
            <div class="form-group">
                <span class="label">Jabatan</span>: <span class="input-line">{{ $data->jabatan }}</span>
            </div>
            <div class="form-group">
                <span class="label">PPIU</span>: <span class="input-line">{{ $data->ppiuname }}</span>
            </div>
            <div class="form-group">
                <span class="label">Alamat & Hp</span>: <span class="input-line">{{ $data->address_phone }}</span>
            </div>
            <div class="form-group">
                <span class="label">Kab/Kota</span>: <span class="input-line">{{ $data->kab_kota }}</span>
            </div>
            <div class="form-group">
                <span class="label">Jumlah Jamaah</span>: <span class="input-line">{{ $data->people }} Orang</span>
            </div>
            <div class="form-group">
                <span class="label">Paket</span>: <span class="input-line">{{ $data->days }} Hari</span>
            </div>
            <div class="form-group">
                <span class="label">Harga per Orang</span>: Rp <span class="input-line">
                    {{ number_format($data->price, 2, ',', '.') }}</span>
            </div>
            <div class="form-group">
                <span class="label">Tanggal Berangkat</span>: <span class="input-line">{{ $formattedDate }}</span>
            </div>
            <div class="form-group">
                <span class="label">Nama Airline</span>: <span class="input-line">{{ $data->airlines }}</span>
            </div>
            <div class="form-group">
                <span class="label">Tanggal Kepulangan</span>: <span class="input-line">{{ $formattedReturnDate }}</span>
            </div>
            <div class="form-group">
                <span class="label">Nama Airline Kepulangan</span>: <span class="input-line">{{ $data->airlines2 }}</span>
            </div>

            <p class="after-fields">Menerangkan bahwa kami memberangkatkan jamaah umroh sesuai dengan 5 PASTI UMROH
                dan sudah terdaftar di Siskopatuh Kementerian Haji dan Umroh RI. Kami mengetahui Regulasi dan sanksi
                terkait penyelenggaraan Umroh dan haji Khusus sesuai :</p>

            <ol type="A">
                <li>Undang-Undang Nomor 8 Tahun 2019 Tentang Penyelenggaran Ibadah haji dan Umrah Bab XI tentang
                    Larangan pasal 113 s.d 119 dan Bab XII tentang Ketentuan Pidana pasal 120 s.s 126.</li>
                <li>Undang-Undang Nomor 11 Tahun 2020 Tentang Cipta Kerja pada Paragraf 14 (keagamaan) Pasal 126
                    "Dalam hal PPIU yang melakukan tindakan sebagimana dimaksud dalam Pasal 119A dalam waktu paling
                    lama 5 (lima) hari tidak memulangkan Jemaah Umroh ke tanah air, PPIU dipidana dengan pidana denda
                    paling lama 10 (sepuluh) tahun atau pidana denda paling banyak Rp. 10.000.000.000,- (sepuluh
                    milyar)</li>
            </ol>

            <p>Demikian Berita Acara pelaporan keberangkatan jamaah umroh.</p>
        </div>

        <div class="sheet-bottom">
            <div class="signatures">
                <div class="signature">
                    Petugas Satgas Umrah<br>
                    Kanwil Kementerian Haji dan Umroh Provinsi NTB
                    @if ($data->status === 'diterima' && $kanwilQrCodeData)
                        <img src="{{ $kanwilQrCodeData }}" alt="QR Code Verifikasi Kanwil">
                    @else
                        <span class="signature-space"></span>
                    @endif
                </div>
                <div class="signature">
                    Petugas PPIU<br>
                    @if ($travelQrCodeData)
                        <img src="{{ $travelQrCodeData }}" alt="QR Code Verifikasi Travel">
                    @else
                        <span class="signature-space"></span>
                    @endif
                    <br>
                    {{ $data->name }}
                </div>
            </div>

            @if ($data->status === 'diterima' && !empty($token))
                <div class="doc-footer">
                    <div class="doc-footer__text">
                        Dokumen ini telah diverifikasi secara elektronik melalui Sistem Pengawasan Haji dan Umrah
                        (PANTAU) Kanwil Kementerian Haji dan Umrah Provinsi NTB.
                        <div class="doc-footer__token"><strong>Token :</strong> {{ $token }}</div>
                    </div>
                </div>
            @endif
        </div>
    </section>
</body>

</html>
