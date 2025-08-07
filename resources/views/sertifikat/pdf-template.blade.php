<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }
        
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12px;
            line-height: 1.2;
            margin: 0;
            padding: 0;
        }
        
        .certificate {
            width: 100%;
            height: 100%;
            position: relative;
        }
        
        .background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            @php
                $backgroundPath = storage_path('app/public/sertifikat/Picture1.png');
                $backgroundImage = '';
                if (file_exists($backgroundPath)) {
                    $backgroundImage = 'data:image/png;base64,' . base64_encode(file_get_contents($backgroundPath));
                }
            @endphp
            @if($backgroundImage)
            background-image: url('{{ $backgroundImage }}');
            @endif
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            z-index: -1;
        }
        
        .content {
            position: relative;
            z-index: 1;
            padding: 20px;
        }
        
        .doc-number {
            position: absolute;
            top: 20px;
            right: 20px;
            font-weight: bold;
        }
        
        .center-number {
            text-align: center;
            font-weight: bold;
            font-style: italic;
            margin-top: 100px;
            margin-bottom: 20px;
        }
        
        .main-text {
            margin-top: 20px;
            margin-left: 20px;
            margin-right: 20px;
        }
        
        .keputusan {
            margin-bottom: 15px;
            font-weight: bold;
        }
        
        .detail-row {
            display: flex;
            margin-bottom: 8px;
        }
        
        .label {
            width: 150px;
            font-weight: bold;
        }
        
        .colon {
            width: 15px;
        }
        
        .value {
            flex: 1;
            font-weight: bold;
        }
        
        .purpose {
            margin-top: 15px;
            font-weight: bold;
        }
        
        .signature {
            position: absolute;
            bottom: 40px;
            right: 20px;
            text-align: right;
            width: 250px;
        }
        
        .location {
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .title {
            margin-bottom: 20px;
            font-weight: bold;
        }
        
        .qr {
            text-align: center;
            margin-bottom: 10px;
        }
        
        .qr-code {
            width: 40px;
            height: 40px;
            margin: 0 auto;
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
        }
        
        .name {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 5px;
        }
        
        .nip {
            text-align: center;
            font-weight: bold;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="background"></div>
        
        <div class="content">
            <div class="doc-number">No. {{ $sertifikat->nomor_dokumen }}</div>
            
            <div class="center-number">
                @php
                    preg_match('/B-(\d+)\/Kw\.18\.01\/HJ\.00\/2\/(\d{2})\/(\d{4})$/', $sertifikat->nomor_surat, $matches);
                    $nomor_urut = $matches[1] ?? '1';
                    $bulan = $matches[2] ?? Carbon::now()->format('m');
                    $tahun = $matches[3] ?? Carbon::now()->format('Y');
                @endphp
                NOMOR: B-{{ $nomor_urut }}/Kw.18.01/HJ.00/2/{{ $bulan }}/{{ $tahun }}
            </div>
            
            <div class="main-text">
                <div class="keputusan">
                    Berdasarkan Keputusan Kepala Kantor Wilayah Kementerian Agama Provinsi Nusa Tenggara Barat Nomor : 226 Tahun 2021 tanggal 09 Maret 2021 diberikan kepada :
                </div>
                
                <div class="detail-row">
                    <div class="label">Nama PPIU</div>
                    <div class="colon">:</div>
                    <div class="value">{{ $sertifikat->nama_ppiu }}</div>
                </div>
                
                <div class="detail-row">
                    <div class="label">Nama Kepala Cabang</div>
                    <div class="colon">:</div>
                    <div class="value">{{ $sertifikat->nama_kepala }}</div>
                </div>
                
                <div class="detail-row">
                    <div class="label">Alamat Kantor</div>
                    <div class="colon">:</div>
                    <div class="value">{{ $sertifikat->alamat }}</div>
                </div>
                
                <div class="detail-row">
                    <div class="label">Tanggal diterbitkannya</div>
                    <div class="colon">:</div>
                    <div class="value">{{ \App\Helpers\DateHelper::formatIndonesiaWithMonth($sertifikat->tanggal_tandatangan) }}</div>
                </div>
                
                <div class="purpose">
                    sebagai Penyelenggara Perjalanan Ibadah Umrah
                </div>
            </div>
            
            <div class="signature">
                <div class="location">
                    Mataram, {{ \App\Helpers\DateHelper::formatIndonesiaWithMonth($sertifikat->tanggal_tandatangan) }}
                </div>
                
                <div class="title">
                    Kepala Kantor Wilayah Kementerian Agama<br>
                    Provinsi Nusa Tenggara Barat,
                </div>
                
                <div class="qr">
                    @if($sertifikat->qrcode_path && Storage::disk('public')->exists($sertifikat->qrcode_path))
                        @php
                            $qrCodePath = storage_path('app/public/' . $sertifikat->qrcode_path);
                            if (file_exists($qrCodePath)) {
                                $qrCodeData = base64_encode(file_get_contents($qrCodePath));
                                $qrCodeMime = mime_content_type($qrCodePath);
                            }
                        @endphp
                        @if(isset($qrCodeData))
                        <div class="qr-code" style="background-image: url('data:{{ $qrCodeMime }};base64,{{ $qrCodeData }}');"></div>
                        @endif
                    @endif
                </div>
                
                @php
                    $settings = \App\Models\SertifikatSetting::first();
                    $nama_penandatangan = $settings ? $settings->nama_penandatangan : 'Drs. H. Ahmad Hidayat, M.Pd';
                    $nip_penandatangan = $settings ? $settings->nip_penandatangan : '196501011990031001';
                @endphp
                
                <div class="name">{{ $nama_penandatangan }}</div>
                <div class="nip">NIP. {{ $nip_penandatangan }}</div>
            </div>
        </div>
    </div>
</body>
</html>
