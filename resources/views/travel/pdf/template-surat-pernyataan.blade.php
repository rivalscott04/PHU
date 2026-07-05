<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Template Surat Pernyataan BA Pemberangkatan</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; line-height: 1.6; }
        h1 { font-size: 16px; text-align: center; text-decoration: underline; }
        .meta { margin: 20px 0; }
        ol { padding-left: 18px; }
        .signature { margin-top: 40px; text-align: right; }
    </style>
</head>
<body>
    <h1>SURAT PERNYATAAN KEBERANGKATAN JAMAAH</h1>

    <div class="meta">
        <p>Yang bertanda tangan di bawah ini:</p>
        <table>
            <tr><td width="140">Nama</td><td>: {{ $pimpinan }}</td></tr>
            <tr><td>Jabatan</td><td>: Direktur / Penanggung Jawab</td></tr>
            <tr><td>PPIU</td><td>: {{ $penyelenggara }}</td></tr>
            <tr><td>Kab/Kota</td><td>: {{ $kabKota }}</td></tr>
        </table>
    </div>

    <p>Dengan ini menyatakan bahwa:</p>
    <ol>
        <li>Data keberangkatan jamaah yang dilaporkan melalui sistem BA Pemberangkatan adalah benar dan dapat dipertanggungjawabkan.</li>
        <li>Jamaah yang berangkat telah terdaftar dan memenuhi persyaratan administrasi sesuai ketentuan yang berlaku.</li>
        <li>Travel bersedia menerima pemeriksaan dan pengawasan dari Kanwil Kementerian Haji dan Umrah NTB apabila diperlukan.</li>
    </ol>

    <p>Demikian surat pernyataan ini dibuat untuk digunakan sebagaimana mestinya.</p>

    <div class="signature">
        <p>{{ $kabKota }}, ........................</p>
        <p style="margin-top: 60px;"><strong>{{ $pimpinan }}</strong><br>Direktur</p>
    </div>
</body>
</html>
