<!DOCTYPE html>
<html lang="id" class="no-js">

<head>
    <meta charset="UTF-8">
    <title>Berita Acara</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.4.1/paper.css">
    <style>
        @page {
            size: F4;
            margin: 0;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .sheet {
            margin: 0;
            overflow: hidden;
            position: relative;
            box-sizing: border-box;
            page-break-after: always;
            padding: 15mm;
            min-height: 100vh;
        }

        .header {
            text-align: center;
            font-size: 12pt;
            line-height: 1.2;
            margin-bottom: 5mm;
        }

        .logo {
            width: 60px;
            position: absolute;
            left: 15mm;
            top: 15mm;
        }

        .letterhead {
            border-bottom: 2px solid black;
            padding-bottom: 2mm;
            margin-bottom: 3mm;
        }

        .title {
            text-align: center;
            font-weight: bold;
            margin-top: 2mm;
            margin-bottom: 3mm;
            font-size: 11pt;
            line-height: 1.2;
            font-family: Arial, sans-serif;
        }

        .content {
            margin: 2mm 0;
            font-size: 11pt;
            line-height: 1.2;
            font-family: Arial, sans-serif;
        }

        .form-group {
            margin: 1mm 0;
            display: flex;
            align-items: center;
            line-height: 1.3;
            font-family: Arial, sans-serif;
        }

        .label {
            width: 180px;
            flex-shrink: 0;
            font-family: Arial, sans-serif;
        }

        .input-line {
            flex-grow: 1;
            margin: 0 2mm;
            min-height: 11pt;
            font-family: Arial, sans-serif;
        }

        .footer {
            margin-top: 80px;
            display: flex;
            justify-content: space-between;
            text-align: center;
            font-size: 11pt;
            line-height: 1.2;
            margin-inline: -35px;
            font-family: Arial, sans-serif;
        }

        .signature {
            width: 40%;
            font-family: Arial, sans-serif;
        }

        ol {
            padding-left: 5mm;
            margin: 1mm 0;
            font-family: Arial, sans-serif;
        }

        li {
            text-align: justify;
            margin-bottom: 2mm;
            line-height: 1.3;
            font-family: Arial, sans-serif;
        }

        p {
            text-align: justify;
            margin: 3mm 0;
            line-height: 1.4;
            font-family: Arial, sans-serif;
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
            Nomor : B- &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/ Kw.
            18.04/2/Hj.00/{{ $month }}/{{ now()->year }}
        </div>

        <div class="content">

            <p>Pada hari ini <b>{{ $dayName }}</b>, tanggal <b>{{ $day }}</b>, bulan <b>{{ $monthYear }}</b>, tahun
                <b>{{ $yearInWords }}</b>,
                yang bertanda tangan dibawah ini :
            </p>

            <p style="margin-bottom: 2mm;"></p>

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

            <p style="margin-bottom: 2mm;"></p>

            <p>Menerangkan bahwa kami memberangkatkan jamaah umroh sesuai dengan 5 PASTI
                UMROH
                dan sudah terdaftar di Siskopatuh Kementerian Agama RI. Kami mengetahui Regulasi dan sanksi
                terkait
                penyelenggaraan Umroh dan haji Khusus sesuai :</p>

            <ol type="A">
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
