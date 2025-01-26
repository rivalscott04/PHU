<!DOCTYPE html>
<html lang="id" class="no-js">

<head>
    <meta charset="UTF-8">
    <title>Berita Acara</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.4.1/paper.css">
    <style>
        @page {
            size: F4
        }

        body {
            margin: 0;
        }

        .sheet {
            margin: 0;
            overflow: hidden;
            position: relative;
            box-sizing: border-box;
            page-break-after: always;
            padding: 20mm;
        }

        .header {
            text-align: center;
            font-size: 14pt;
            line-height: 1.3;
        }

        .logo {
            width: 80px;
            position: absolute;
            left: 20mm;
            top: 20mm;
        }

        .letterhead {
            border-bottom: 2px solid black;
            padding-bottom: 2mm;
        }

        .title {
            text-align: center;
            font-weight: bold;
            margin-top: 2mm;
            font-size: 14pt;
            line-height: 1.3;
        }

        .content {
            margin: 3mm 0;
            font-size: 14pt;
            line-height: 1.3;
        }

        .form-group {
            margin: 0.5mm 0;
            display: flex;
            align-items: center;
        }

        .label {
            width: 200px;
            flex-shrink: 0;
        }

        .input-line {
            flex-grow: 1;
            margin: 0 2mm;
            min-height: 14pt;
        }

        .footer {
            margin-top: 140px;
            display: flex;
            justify-content: space-between;
            text-align: center;
            font-size: 14pt;
            line-height: 1.3;
            margin-inline: -35px;
        }

        .signature {
            width: 40%;
        }

        ol {
            padding-left: 5mm;
            margin: 2mm 0;
        }

        li {
            text-align: justify;
            margin-bottom: 2mm;
        }

        p {
            text-align: justify;
            margin: 2mm 0;
        }
    </style>
</head>

<body class="F4">
    <section class="sheet">
        <div class="letterhead">
            <img src="{{ asset('images/kemenag.png') }}" alt="Logo" class="logo">
            <div class="header">
                <strong>KEMENTERIAN AGAMA REPUBLIK INDONESIA<br>
                    KANTOR WILAYAH KEMENTERIAN AGAMA<br>
                    PROVINSI NUSA TENGGARA BARAT<br></strong>
                <span style="font-size: 12pt;">JL. Udayana No. 6 Mataram Telp. (0370) 622317 Faksimili (0370) 622317<br>
                    Website : www.ntb.Kemenag.go.id
                </span>
            </div>
        </div>

        <div class="title">
            BERITA ACARA<br>
            PELAPORAN PEMBERANGKATAN JAMAAH UMROH<br>
            Nomor : B- &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/ Kw. 18.04/2/Hj.00/ &nbsp;&nbsp;&nbsp;&nbsp;/{{ now()->year }}
        </div>

        <div class="content">

            <p>Pada hari ini {{ now()->translatedFormat('l') }}, tanggal {{ now()->day }}, bulan
                {{ now()->translatedFormat('F') }}, tahun {{ $yearInWords }},
                yang bertanda tangan dibawah ini :
            <p>
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
                <span class="label">Paket</span>: <span class="input-line">{{ $data->package }} Hari</span>
            </div>
            <div class="form-group">
                <span class="label">Harga</span>: Rp <span class="input-line">
                    {{ number_format($data->price, 2, ',', '.') }}</span>
            </div>
            <div class="form-group">
                <span class="label">Tanggal Berangkat</span>: <span class="input-line">{{ $formattedDate }}</span>
            </div>
            <div class="form-group">
                <span class="label">Nama Airline</span>: <span class="input-line">{{ $data->airlines }}</span>
            </div>
            <div class="form-group">
                <span class="label">Tanggal Kepulangan</span>: <span
                    class="input-line">{{ $formattedReturnDate }}</span>
            </div>
            <div class="form-group">
                <span class="label">Nama Airline Kepulangan</span>: <span
                    class="input-line">{{ $data->airlines2 }}</span>
            </div>

            <p style="font-size:13pt">Menerangkan bahwa kami memberangkatkan jamaah umroh sesuai dengan 5 PASTI
                UMROH
                dan sudah terdaftar di Siskopatuh Kementerian Agama RI. Kami mengetahui Regulasi dan sanksi
                terkait
                penyelenggaraan Umroh dan haji Khusus sesuai :</p>

            <ol type="A" style="font-size:13pt">
                <li>Undang-Undang Nomor 8 Tahun 2019 Tentang Penyelenggaran Ibadah haji dan Umrah Bab XI tentang
                    Larangan pasal 113 s.d 119 dan Bab XII tentang Ketentuan Pidana pasal 120 s.s 126.</li>
                <li>Undang-Undang Nomor 11 Tahun 2020 Tentang Cipta Kerja pada Paragraf 14 (keagamaan) Pasal 126
                    "Dalam
                    hal PPIU yang melakukan tindakan sebagimana dimaksud dalam Pasal 119A dalam waktu paling
                    lama 5
                    (lima)
                    hari tidak memulangkan Jemaah Umroh ke tanah air, PPIU dipidana dengan pidana denda
                    paling
                    lama 10 (sepuluh) tahun atau pidana denda paling banyak Rp. 10.000.000.000,- (sepuluh
                    milyar)
                </li>
            </ol>

            <p>Demikian Berita Acara pelaporan keberangkatan jamaah umroh.</p>
        </div>

        <div class="footer">
            <div class="signature">
                Petugas Satgas Umrah<br>
                Kanwil Kementerian Agama Prov. NTB<br><br>
                <span class="input-line" style="width:100px;display:inline-block"></span>
            </div>
            <div class="signature" style="margin-left: 50px;">
                Petugas PPIU<br>
                <span class="input-line" style="width:200px;display:inline-block"></span><br><br><br><br>
                <span class="input-line" style="width:100px;display:inline-block">{{ $data->name }}</span>
            </div>
        </div>
    </section>
</body>

</html>
