<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Pengaduan - {{ $pengaduan->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #2563eb;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .content {
            margin-bottom: 30px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section h3 {
            color: #2563eb;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        .info-row {
            display: flex;
            margin-bottom: 10px;
        }
        .label {
            font-weight: bold;
            width: 150px;
            color: #374151;
        }
        .value {
            flex: 1;
            color: #111827;
        }
        .status-completed {
            color: #059669;
            font-weight: bold;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
            color: #666;
            font-size: 12px;
        }
        .signature {
            margin-top: 40px;
            text-align: right;
        }
        .signature-line {
            border-top: 1px solid #000;
            width: 200px;
            margin-top: 50px;
            margin-left: auto;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>HASIL PENANGANAN PENGADUAN</h1>
        <p>Kementerian Agama Republik Indonesia</p>
        <p>Direktorat Jenderal Penyelenggaraan Haji dan Umrah</p>
        <p>Nomor: {{ $pengaduan->id }}/{{ date('Y') }}</p>
    </div>

    <div class="content">
        <div class="section">
            <h3>INFORMASI PENGADUAN</h3>
            <div class="info-row">
                <div class="label">Nomor Pengaduan:</div>
                <div class="value">{{ $pengaduan->id }}</div>
            </div>
            <div class="info-row">
                <div class="label">Tanggal Pengaduan:</div>
                <div class="value">{{ $pengaduan->created_at->format('d/m/Y H:i') }}</div>
            </div>
            <div class="info-row">
                <div class="label">Nama Pengadu:</div>
                <div class="value">{{ $pengaduan->nama_pengadu }}</div>
            </div>
            <div class="info-row">
                <div class="label">Travel yang Diadukan:</div>
                <div class="value">{{ $pengaduan->travel->Penyelenggara ?? 'Tidak tersedia' }}</div>
            </div>
            <div class="info-row">
                <div class="label">Status:</div>
                <div class="value status-completed">SELESAI DIPROSES</div>
            </div>
            <div class="info-row">
                <div class="label">Tanggal Selesai:</div>
                <div class="value">{{ $pengaduan->completed_at ? $pengaduan->completed_at->format('d/m/Y H:i') : 'Tidak tersedia' }}</div>
            </div>
        </div>

        <div class="section">
            <h3>HAL PENGADUAN</h3>
            <div style="background: #f9fafb; padding: 15px; border-radius: 5px; border-left: 4px solid #2563eb;">
                {{ $pengaduan->hal_aduan }}
            </div>
        </div>

        @if($pengaduan->admin_notes)
        <div class="section">
            <h3>CATATAN ADMIN</h3>
            <div style="background: #f0f9ff; padding: 15px; border-radius: 5px; border-left: 4px solid #0ea5e9;">
                {{ $pengaduan->admin_notes }}
            </div>
        </div>
        @endif

        <div class="section">
            <h3>HASIL PENANGANAN</h3>
            <div style="background: #f0fdf4; padding: 15px; border-radius: 5px; border-left: 4px solid #16a34a;">
                <p><strong>Pengaduan telah ditangani dan diselesaikan sesuai dengan prosedur yang berlaku.</strong></p>
                <p>Dokumen ini merupakan bukti bahwa pengaduan telah diproses dan diselesaikan oleh pihak yang berwenang.</p>
            </div>
        </div>
    </div>

    <div class="signature">
        <p>Jakarta, {{ date('d F Y') }}</p>
        <p>Petugas Penanganan Pengaduan</p>
        <div class="signature-line"></div>
        <p style="margin-top: 10px;">(_________________)</p>
    </div>

    <div class="footer">
        <p>Dokumen ini dibuat secara otomatis oleh sistem</p>
        <p>Dicetak pada: {{ date('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
