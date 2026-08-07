<?php

namespace App\Helpers;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as ValidatorFactory;
use Illuminate\Validation\Rule;
use App\Support\NtbKabupatenMap;

class ValidationHelper
{
    private const LABELS = [
        'Penyelenggara' => 'Nama Penyelenggara',
        'Status' => 'Jenis Izin',
        'Pusat' => 'No. SK / NIB Pusat',
        'Tanggal' => 'Tanggal SK',
        'nilai_akreditasi' => 'Nilai Akreditasi',
        'tanggal_akreditasi' => 'Tanggal Akreditasi',
        'lembaga_akreditasi' => 'Lembaga Akreditasi',
        'Pimpinan' => 'Nama Pimpinan / Direktur',
        'Telepon' => 'Telepon Kantor',
        'alamat_kantor_lama' => 'Alamat Kantor (Lama)',
        'alamat_kantor_baru' => 'Alamat Kantor (Saat Ini)',
        'kab_kota' => 'Kabupaten / Kota',
        'pic_nama' => 'Nama Lengkap PIC',
        'pic_email' => 'Email PIC',
        'pic_nomor_hp' => 'Nomor HP (WhatsApp)',
        'password' => 'Password',
        'password_confirmation' => 'Ulangi Password',
        'dokumen_sk' => 'Scan SK / Izin Operasional',
        'dokumen_akreditasi' => 'Scan Sertifikat Akreditasi',
        'nama_pengadu' => 'Nama Pengadu',
        'travels_id' => 'Travel yang Diadukan',
        'hal_aduan' => 'Hal yang Diadukan',
        'berkas_aduan' => 'Lampiran Aduan',
        'nama' => 'Nama Lengkap',
        'email' => 'Email',
        'nomor_hp' => 'Nomor HP',
        'role' => 'Peran Pengguna',
        'kabupaten' => 'Kabupaten',
        'travel_id' => 'Travel',
        'nik' => 'NIK',
        'alamat' => 'Alamat',
        'file' => 'File Excel',
        'excel_file' => 'File Excel',
        'email_or_phone' => 'Email atau Nomor HP',
        'name' => 'Nama Penanggung Jawab',
        'jabatan' => 'Jabatan',
        'ppiuname' => 'Nama PPIU',
        'address_phone' => 'Alamat dan Telepon',
        'jamaah_ids' => 'Daftar Jamaah',
        'days' => 'Jumlah Hari',
        'price' => 'Harga Paket',
        'datetime' => 'Tanggal Keberangkatan',
        'airlines' => 'Maskapai Pergi',
        'returndate' => 'Tanggal Kembali',
        'airlines2' => 'Maskapai Pulang',
        'pdf_file' => 'File PDF',
        'status' => 'Status',
        'admin_notes' => 'Catatan Admin',
        'registration_notes' => 'Alasan Penolakan',
        'pusat' => 'No. SK Pusat',
        'pimpinan_pusat' => 'Pimpinan Pusat',
        'alamat_pusat' => 'Alamat Pusat',
        'SK_BA' => 'No. SK / BA',
        'tanggal' => 'Tanggal',
        'pimpinan_cabang' => 'Pimpinan Cabang',
        'alamat_cabang' => 'Alamat Cabang',
        'telepon' => 'Telepon',
        'support_phone' => 'Nomor Telepon Support',
        'support_email' => 'Email Support',
        'nama_lengkap' => 'Nama Lengkap',
        'no_ktp' => 'NIK',
        'tempat_lahir' => 'Tempat Lahir',
        'tanggal_lahir' => 'Tanggal Lahir',
        'jenis_kelamin' => 'Jenis Kelamin',
        'kota' => 'Kota',
        'kecamatan' => 'Kecamatan',
        'provinsi' => 'Provinsi',
        'kode_pos' => 'Kode Pos',
        'no_hp' => 'Nomor HP',
        'nama_ayah' => 'Nama Ayah',
        'pekerjaan' => 'Pekerjaan',
        'pendidikan_terakhir' => 'Pendidikan Terakhir',
        'status_pernikahan' => 'Status Pernikahan',
        'pergi_haji' => 'Riwayat Haji',
        'golongan_darah' => 'Golongan Darah',
        'alergi' => 'Alergi',
        'no_paspor' => 'Nomor Paspor',
        'tanggal_berlaku_paspor' => 'Masa Berlaku Paspor',
        'tempat_terbit_paspor' => 'Tempat Terbit Paspor',
        'nomor_porsi' => 'Nomor Porsi',
        'tahun_pendaftaran' => 'Tahun Pendaftaran',
        'catatan_khusus' => 'Catatan Khusus',
        'dokumen_ktp' => 'Scan KTP',
        'dokumen_kk' => 'Scan Kartu Keluarga',
        'dokumen_paspor' => 'Scan Paspor',
        'dokumen_foto' => 'Foto',
        'surat_keterangan' => 'Surat Keterangan',
        'bukti_setor_bank' => 'Bukti Setor Bank',
        'status_pendaftaran' => 'Status Pendaftaran',
        'status_verifikasi_bukti' => 'Status Verifikasi Bukti',
        'catatan_verifikasi' => 'Catatan Verifikasi',
        'berkas_pengunduran' => 'Berkas Pengunduran Diri',
        'current_password' => 'Password Saat Ini',
        'new_password' => 'Password Baru',
        'new_password_confirmation' => 'Ulangi Password Baru',
        'address' => 'Alamat',
        'city' => 'Kota',
        'country' => 'Negara',
        'postal' => 'Kode Pos',
        'about' => 'Tentang Saya',
        'qr_data' => 'Data QR Code',
        'token' => 'Token Verifikasi',
        'remarks' => 'Catatan',
        'finding_id' => 'Temuan',
        'description' => 'Deskripsi',
        'attachment' => 'Lampiran',
        'inspection_date' => 'Tanggal Pengawasan',
        'inspection_type' => 'Jenis Pengawasan',
        'notes' => 'Catatan',
        'category_id' => 'Kategori Checklist',
        'title' => 'Judul',
        'input_type' => 'Tipe Input',
        'weight' => 'Bobot',
        'required' => 'Wajib Diisi',
        'sort_order' => 'Urutan',
        'is_active' => 'Status Aktif',
        'category' => 'Kategori',
        'severity' => 'Tingkat Keparahan',
        'recommendation' => 'Rekomendasi',
        'deadline' => 'Batas Waktu',
        'items' => 'Daftar Checklist',
        'items.*.id' => 'Item Checklist',
        'items.*.answer' => 'Jawaban Checklist',
        'items.*.note' => 'Catatan Checklist',
        'id' => 'Notifikasi',
        'cabang_id' => 'Cabang Travel',
        'nama_ppiu' => 'Nama PPIU',
        'nama_kepala' => 'Nama Kepala',
        'tanggal_diterbitkan' => 'Tanggal Diterbitkan',
        'nomor_surat' => 'Nomor Surat',
        'nomor_dokumen' => 'Nomor Dokumen',
        'bulan_surat' => 'Bulan Surat',
        'tahun_surat' => 'Tahun Surat',
        'tanggal_tandatangan' => 'Tanggal Tanda Tangan',
        'jenis_lokasi' => 'Jenis Lokasi',
        'nama_penandatangan' => 'Nama Penandatangan',
        'nip_penandatangan' => 'NIP Penandatangan',
        'jabatan_penandatangan' => 'Jabatan Penandatangan',
        'pengawas_scope' => 'Cakupan Pengawas',
        'pengawas_kabupatens' => 'Kabupaten Pengawas',
        'username' => 'Nama Pengguna',
        'terms' => 'Persetujuan Syarat dan Ketentuan',
    ];

    public static function label(string $field): string
    {
        return self::LABELS[$field] ?? self::humanize($field);
    }

    /**
     * @param  list<string>  $fields
     * @return array<string, string>
     */
    public static function attributes(array $fields): array
    {
        $attributes = [];

        foreach ($fields as $field) {
            $attributes[$field] = self::label($field);
        }

        return $attributes;
    }

    /**
     * @param  array<string, mixed>  $rules
     * @return list<string>
     */
    public static function fieldsFromRules(array $rules): array
    {
        return array_keys($rules);
    }

    /**
     * @param  list<string>  $fields
     * @param  array<string, string>  $overrides
     * @return array<string, string>
     */
    public static function messages(array $fields, array $overrides = []): array
    {
        $messages = [];

        foreach ($fields as $field) {
            $messages += self::contextualOverrides($field);
            $messages += self::defaultMessagesFor($field);
        }

        return array_merge($messages, $overrides);
    }

    /**
     * @param  array<string, mixed>  $rules
     * @param  array<string, string>  $overrides
     * @return array<string, mixed>
     */
    public static function validate(Request $request, array $rules, array $overrides = []): array
    {
        $fields = self::fieldsFromRules($rules);

        return $request->validate(
            $rules,
            self::messages($fields, $overrides),
            self::attributes($fields)
        );
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $rules
     * @param  array<string, string>  $overrides
     */
    public static function makeValidator(array $data, array $rules, array $overrides = []): Validator
    {
        $fields = self::fieldsFromRules($rules);

        return ValidatorFactory::make(
            $data,
            $rules,
            self::messages($fields, $overrides),
            self::attributes($fields)
        );
    }

    /**
     * @return array<string, string>
     */
    public static function fileMaxMb(string $field, float $megabytes): array
    {
        $label = fmod($megabytes, 1.0) === 0.0 ? (string) (int) $megabytes : (string) $megabytes;

        return [
            "{$field}.max" => "Ukuran :attribute terlalu besar. Maksimal {$label} MB.",
        ];
    }

    public static function fileMaxKb(float $megabytes): int
    {
        return (int) round($megabytes * 1024);
    }

    public const NIK_LENGTH = 16;

    public const NOMOR_HP_MAX = 16;

    /** Default VARCHAR(255) column limit in migrations. */
    public const VARCHAR_MAX = 255;

    /** Reasonable upper bound for TEXT columns (prevents abuse, still generous). */
    public const TEXT_MAX = 5000;

    public static function varcharRule(bool $required = true): string
    {
        return ($required ? 'required' : 'nullable').'|string|max:'.self::VARCHAR_MAX;
    }

    public static function textRule(bool $required = true): string
    {
        return ($required ? 'required' : 'nullable').'|string|max:'.self::TEXT_MAX;
    }

    /** @var list<string> */
    public const NIK_FIELD_NAMES = ['nik', 'no_ktp'];

    /** @var list<string> */
    public const PHONE_FIELD_NAMES = ['nomor_hp', 'no_hp', 'pic_nomor_hp', 'telepon', 'Telepon'];

    /** 08 + 6..14 digit = total 8..16 karakter */
    public const NOMOR_HP_REGEX = '/^08\d{6,14}$/';

    public static function isNikField(string $name): bool
    {
        return in_array($name, self::NIK_FIELD_NAMES, true);
    }

    public static function isPhoneField(string $name): bool
    {
        return in_array($name, self::PHONE_FIELD_NAMES, true);
    }

    /**
     * @return array{nik: array{fields: list<string>, max: int}, phone: array{fields: list<string>, max: int}}
     */
    public static function inputLimitConfig(): array
    {
        return [
            'nik' => [
                'fields' => self::NIK_FIELD_NAMES,
                'max' => self::NIK_LENGTH,
            ],
            'phone' => [
                'fields' => self::PHONE_FIELD_NAMES,
                'max' => self::NOMOR_HP_MAX,
            ],
        ];
    }

    /**
     * @param  array<string, string|bool|null>  $extra
     * @return array<string, string|bool>
     */
    public static function nikInputAttributes(array $extra = []): array
    {
        return array_filter(array_merge([
            'maxlength' => (string) self::NIK_LENGTH,
            'inputmode' => 'numeric',
            'data-digits-only' => (string) self::NIK_LENGTH,
            'autocomplete' => 'off',
            'spellcheck' => 'false',
        ], $extra), static fn ($value) => $value !== null && $value !== false);
    }

    /**
     * @param  array<string, string|bool|null>  $extra
     * @return array<string, string|bool>
     */
    public static function nomorHpInputAttributes(array $extra = []): array
    {
        return array_filter(array_merge([
            'maxlength' => (string) self::NOMOR_HP_MAX,
            'inputmode' => 'numeric',
            'data-digits-only' => (string) self::NOMOR_HP_MAX,
            'placeholder' => '08xxxxxxxxxx',
            'autocomplete' => 'tel',
        ], $extra), static fn ($value) => $value !== null && $value !== false);
    }

    /**
     * @param  array<string, string|bool>  $attributes
     */
    public static function renderInputAttributes(array $attributes): string
    {
        $parts = [];

        foreach ($attributes as $key => $value) {
            if ($value === false || $value === null) {
                continue;
            }

            if ($value === true) {
                $parts[] = $key;

                continue;
            }

            $parts[] = $key.'="'.e((string) $value).'"';
        }

        return implode(' ', $parts);
    }

    /** @return list<string|\Illuminate\Validation\Rules\Unique> */
    public static function nikRules(): array
    {
        return ['required', 'digits:'.self::NIK_LENGTH];
    }

    /** @return list<string|\Illuminate\Validation\Rules\Unique> */
    public static function nomorHpRules(bool $uniqueInUsers = false, ?int $ignoreUserId = null): array
    {
        $rules = ['required', 'string', 'max:'.self::NOMOR_HP_MAX, 'regex:'.self::NOMOR_HP_REGEX];

        if ($uniqueInUsers) {
            $unique = Rule::unique('users', 'nomor_hp');
            if ($ignoreUserId !== null) {
                $unique->ignore($ignoreUserId);
            }
            $rules[] = $unique;
        }

        return $rules;
    }

    /** @return list<string|\Illuminate\Validation\Rules\Unique> */
    public static function teleponRules(bool $required = true): array
    {
        $rules = ['string', 'max:'.self::NOMOR_HP_MAX];

        if ($required) {
            array_unshift($rules, 'required');
        }

        return $rules;
    }

    /** @return array<string, mixed> */
    public static function travelCompanyDataRules(?int $ignoreTravelId = null): array
    {
        return [
            'Penyelenggara' => ['required', 'string', 'max:255', self::uniquePenyelenggaraRule($ignoreTravelId)],
            'Pusat' => 'required|string|max:255',
            'Tanggal' => 'required|date',
            'nilai_akreditasi' => 'required|string|max:255',
            'tanggal_akreditasi' => 'required|date',
            'lembaga_akreditasi' => 'required|string|max:255',
            'Pimpinan' => 'required|string|max:255',
            'alamat_kantor_lama' => self::textRule(),
            'alamat_kantor_baru' => self::textRule(),
            'Telepon' => ValidationHelper::teleponRules(),
            'kab_kota' => ['required', 'string', Rule::in(NtbKabupatenMap::names())],
            'Status' => 'required|in:PPIU,PIHK',
        ];
    }

    public static function uniquePenyelenggaraRule(?int $ignoreTravelId = null): \Illuminate\Validation\Rules\Unique
    {
        $rule = Rule::unique('travels', 'Penyelenggara')
            ->where(fn ($query) => $query->whereIn('registration_status', ['pending', 'approved']));

        if ($ignoreTravelId !== null) {
            $rule->ignore($ignoreTravelId);
        }

        return $rule;
    }

    /**
     * @return array<string, string>
     */
    public static function excelFileOverrides(string $field = 'file', float $maxMb = 10): array
    {
        return array_merge(
            self::fileMaxMb($field, $maxMb),
            [
                "{$field}.required" => 'Mohon pilih file Excel untuk diunggah.',
                "{$field}.mimes" => 'File harus berformat Excel (.xlsx, .xls) atau CSV.',
            ]
        );
    }

    /**
     * @return array<string, string>
     */
    private static function defaultMessagesFor(string $field): array
    {
        return [
            "{$field}.required" => 'Mohon isi :attribute.',
            "{$field}.string" => ':attribute harus berupa teks.',
            "{$field}.email" => 'Format email tidak valid. Contoh: nama@perusahaan.com',
            "{$field}.date" => 'Mohon pilih tanggal yang valid pada :attribute.',
            "{$field}.max" => ':attribute terlalu panjang. Maksimal :max karakter.',
            "{$field}.min" => ':attribute terlalu pendek. Minimal :min karakter.',
            "{$field}.in" => 'Pilihan :attribute tidak valid. Silakan pilih dari daftar.',
            "{$field}.unique" => ':attribute sudah terdaftar di sistem. Gunakan data lain.',
            "{$field}.confirmed" => 'Konfirmasi :attribute tidak cocok. Pastikan kedua kolom sama.',
            "{$field}.regex" => 'Format :attribute tidak valid. Periksa kembali isian Anda.',
            "{$field}.file" => 'Mohon unggah :attribute.',
            "{$field}.mimes" => ':attribute harus berformat PDF, JPG, atau PNG.',
            "{$field}.integer" => ':attribute harus berupa angka bulat.',
            "{$field}.numeric" => ':attribute harus berupa angka.',
            "{$field}.exists" => 'Data :attribute tidak ditemukan. Silakan pilih ulang.',
            "{$field}.digits" => ':attribute harus :digits digit angka.',
            "{$field}.size" => ':attribute harus :size karakter.',
            "{$field}.array" => ':attribute harus berupa daftar.',
            "{$field}.boolean" => ':attribute tidak valid.',
            "{$field}.uuid" => 'Data :attribute tidak valid.',
            "{$field}.same" => ':attribute harus sama dengan kolom terkait.',
            "{$field}.before" => ':attribute harus sebelum hari ini.',
            "{$field}.after" => ':attribute harus setelah hari ini.',
            "{$field}.after_or_equal" => ':attribute tidak boleh sebelum hari ini.',
            "{$field}.nullable" => ':attribute tidak valid.',
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function contextualOverrides(string $field): array
    {
        if ($field === 'Penyelenggara') {
            return ['Penyelenggara.unique' => 'Nama penyelenggara sudah terdaftar (pending atau aktif).'];
        }

        if (in_array($field, ['nomor_hp', 'pic_nomor_hp', 'no_hp'], true)) {
            $max = self::NOMOR_HP_MAX;

            return [
                "{$field}.regex" => "Nomor HP harus angka, diawali 08, panjang 8 s.d. {$max} digit. Contoh: 081234567890.",
                "{$field}.max" => "Nomor HP terlalu panjang. Maksimal {$max} digit.",
            ];
        }

        if ($field === 'nik') {
            $len = self::NIK_LENGTH;

            return [
                'nik.digits' => "NIK harus tepat {$len} digit angka.",
                'nik.unique' => 'NIK ini sudah terdaftar di travel Anda.',
            ];
        }

        if ($field === 'no_ktp') {
            $len = self::NIK_LENGTH;

            return [
                'no_ktp.digits' => "NIK harus tepat {$len} digit angka.",
                'no_ktp.size' => "NIK harus tepat {$len} digit angka.",
            ];
        }

        if ($field === 'kode_pos') {
            return ['kode_pos.size' => 'Kode pos harus 5 digit.'];
        }

        if (in_array($field, ['file', 'excel_file'], true)) {
            return [
                "{$field}.required" => 'Mohon pilih file Excel untuk diunggah.',
                "{$field}.mimes" => 'File harus berformat Excel (.xlsx, .xls) atau CSV.',
            ];
        }

        if ($field === 'pdf_file') {
            return ['pdf_file.mimes' => 'File harus berformat PDF.'];
        }

        if ($field === 'email_or_phone') {
            return [
                'email_or_phone.required' => 'Mohon isi email atau nomor HP.',
            ];
        }

        if ($field === 'jamaah_ids') {
            return [
                'jamaah_ids.required' => 'Pilih minimal satu jamaah.',
                'jamaah_ids.min' => 'Pilih minimal satu jamaah.',
            ];
        }

        if ($field === 'registration_notes') {
            return ['registration_notes.required' => 'Mohon tuliskan alasan penolakan.'];
        }

        if ($field === 'remarks') {
            return ['remarks.min' => 'Catatan minimal :min karakter. Jelaskan dengan lebih detail.'];
        }

        if ($field === 'hal_aduan') {
            return ['hal_aduan.min' => 'Hal yang diadukan minimal :min karakter.'];
        }

        if ($field === 'nama_pengadu') {
            return ['nama_pengadu.regex' => 'Nama hanya boleh berisi huruf, spasi, tanda hubung, titik, dan apostrof.'];
        }

        if ($field === 'items') {
            return [
                'items.required' => 'Daftar checklist wajib diisi.',
                'items.min' => 'Daftar checklist wajib diisi.',
            ];
        }

        if ($field === 'items.*.id') {
            return ['items.*.id.exists' => 'Item checklist tidak ditemukan.'];
        }

        if ($field === 'attachment') {
            return [
                'attachment.mimes' => 'Lampiran harus berformat PDF, Word, JPG, PNG, atau ZIP.',
            ];
        }

        if ($field === 'dokumen_foto') {
            return ['dokumen_foto.mimes' => 'Foto harus berformat JPG atau PNG.'];
        }

        if ($field === 'terms') {
            return ['terms.required' => 'Mohon setujui syarat dan ketentuan.'];
        }

        if (str_starts_with($field, 'dokumen_') || $field === 'berkas_pengunduran' || $field === 'berkas_aduan') {
            return [
                "{$field}.required" => 'Mohon unggah :attribute.',
            ];
        }

        return [];
    }

    private static function humanize(string $field): string
    {
        $normalized = preg_replace('/\.\*\.?/', ' ', $field) ?? $field;
        $normalized = str_replace('_', ' ', $normalized);

        return ucwords(trim($normalized));
    }
}
