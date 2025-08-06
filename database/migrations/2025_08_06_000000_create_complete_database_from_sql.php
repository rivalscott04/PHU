<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create bap table
        if (!Schema::hasTable('bap')) {
            Schema::create('bap', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('jabatan');
                $table->string('ppiuname');
                $table->string('address_phone');
                $table->string('kab_kota');
                $table->integer('people');
                $table->string('package');
                $table->decimal('price', 10, 2);
                $table->date('datetime');
                $table->string('airlines');
                $table->date('returndate');
                $table->string('airlines2');
                $table->string('pdf_file_path')->nullable();
                $table->enum('status', ['pending', 'diajukan', 'diproses', 'diterima'])->default('pending');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->timestamps();
            });
        }

        // Create failed_jobs table
        if (!Schema::hasTable('failed_jobs')) {
            Schema::create('failed_jobs', function (Blueprint $table) {
                $table->id();
                $table->string('uuid')->unique();
                $table->text('connection');
                $table->text('queue');
                $table->longText('payload');
                $table->longText('exception');
                $table->timestamp('failed_at')->useCurrent();
            });
        }

        // Create jamaah table
        if (!Schema::hasTable('jamaah')) {
            Schema::create('jamaah', function (Blueprint $table) {
                $table->id();
                $table->string('nik', 16);
                $table->string('nama');
                $table->text('alamat');
                $table->string('nomor_hp', 13);
                $table->string('jenis_jamaah');
                $table->timestamps();
            });
        }

        // Create jamaah_haji_khusus table
        if (!Schema::hasTable('jamaah_haji_khusus')) {
            Schema::create('jamaah_haji_khusus', function (Blueprint $table) {
                $table->id();
                $table->foreignId('travel_id')->constrained()->onDelete('cascade');
                $table->string('nama_lengkap');
                $table->string('no_ktp', 16)->unique();
                $table->string('tempat_lahir');
                $table->date('tanggal_lahir');
                $table->enum('jenis_kelamin', ['L', 'P']);
                $table->string('alamat');
                $table->string('kota');
                $table->string('kecamatan')->nullable();
                $table->string('provinsi');
                $table->string('kode_pos', 5);
                $table->string('no_hp', 15);
                $table->string('email')->nullable();
                $table->string('nama_ayah');
                $table->string('pekerjaan');
                $table->string('pendidikan_terakhir');
                $table->enum('status_pernikahan', ['Belum Menikah', 'Menikah', 'Cerai']);
                $table->enum('pergi_haji', ['Belum', 'Sudah'])->nullable();
                $table->string('golongan_darah');
                $table->string('alergi')->nullable();
                $table->string('no_paspor')->nullable();
                $table->date('tanggal_berlaku_paspor')->nullable();
                $table->string('tempat_terbit_paspor')->nullable();
                $table->string('nomor_porsi')->nullable();
                $table->string('tahun_pendaftaran')->nullable();
                $table->enum('status_pendaftaran', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
                $table->text('catatan_khusus')->nullable();
                $table->string('dokumen_ktp')->nullable();
                $table->string('dokumen_kk')->nullable();
                $table->string('dokumen_paspor')->nullable();
                $table->string('dokumen_foto')->nullable();
                $table->string('surat_keterangan')->nullable();
                $table->string('bukti_setor_bank')->nullable();
                $table->enum('status_verifikasi_bukti', ['pending', 'verified', 'rejected'])->default('pending');
                $table->text('catatan_verifikasi')->nullable();
                $table->timestamp('tanggal_verifikasi')->nullable();
                $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
            });
        }

        // Create password_reset_tokens table
        if (!Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        // Create pengaduan table
        if (!Schema::hasTable('pengaduan')) {
            Schema::create('pengaduan', function (Blueprint $table) {
                $table->id();
                $table->string('nama_pengadu');
                $table->foreignId('travels_id')->constrained()->onDelete('cascade');
                $table->text('hal_aduan');
                $table->string('berkas_aduan')->nullable();
                $table->string('status')->default('pending');
                $table->string('pdf_output')->nullable();
                $table->text('admin_notes')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
            });
        }

        // Create pengunduran table
        if (!Schema::hasTable('pengunduran')) {
            Schema::create('pengunduran', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('berkas_pengunduran');
                $table->enum('status', ['pending', 'diajukan', 'diterima'])->default('pending');
                $table->timestamps();
            });
        }

        // Create personal_access_tokens table
        if (!Schema::hasTable('personal_access_tokens')) {
            Schema::create('personal_access_tokens', function (Blueprint $table) {
                $table->id();
                $table->morphs('tokenable');
                $table->string('name');
                $table->string('token', 64)->unique();
                $table->text('abilities')->nullable();
                $table->timestamp('last_used_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            });
        }

        // Create sertifikat table (if not exists)
        if (!Schema::hasTable('sertifikat')) {
            Schema::create('sertifikat', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->unique();
                $table->foreignId('travel_id')->nullable()->constrained()->onDelete('cascade');
                $table->foreignId('cabang_id')->nullable()->constrained('travel_cabang', 'id_cabang')->onDelete('cascade');
                $table->string('nama_ppiu');
                $table->string('nama_kepala');
                $table->text('alamat');
                $table->date('tanggal_diterbitkan')->nullable();
                $table->date('tanggal_tandatangan')->nullable();
                $table->string('nomor_surat')->nullable();
                $table->string('nomor_dokumen')->nullable();
                $table->string('qrcode_path')->nullable();
                $table->string('sertifikat_path')->nullable();
                $table->string('pdf_path')->nullable();
                $table->enum('jenis', ['PPIU', 'PIHK'])->default('PPIU');
                $table->enum('jenis_lokasi', ['pusat', 'cabang'])->default('pusat');
                $table->enum('status', ['active', 'expired', 'revoked'])->default('active');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // Create sertifikat_settings table
        if (!Schema::hasTable('sertifikat_settings')) {
            Schema::create('sertifikat_settings', function (Blueprint $table) {
                $table->id();
                $table->string('nama_penandatangan')->nullable();
                $table->string('nip_penandatangan')->nullable();
                $table->timestamps();
            });
        }

        // Create travels table
        if (!Schema::hasTable('travels')) {
            Schema::create('travels', function (Blueprint $table) {
                $table->id();
                $table->string('Penyelenggara');
                $table->string('Pusat');
                $table->date('Tanggal');
                $table->string('nilai_akreditasi')->nullable();
                $table->date('tanggal_akreditasi')->nullable();
                $table->string('lembaga_akreditasi')->nullable();
                $table->string('Pimpinan');
                $table->text('alamat_kantor_lama');
                $table->text('alamat_kantor_baru')->nullable();
                $table->string('Telepon', 20);
                $table->enum('Status', ['PIHK', 'PPIU']);
                $table->json('capabilities')->nullable();
                $table->boolean('can_haji')->default(0);
                $table->boolean('can_umrah')->default(1);
                $table->text('description')->nullable();
                $table->string('license_number')->nullable();
                $table->date('license_expiry')->nullable();
                $table->string('kab_kota');
                $table->timestamps();
            });
        }

        // Create travel_cabang table
        if (!Schema::hasTable('travel_cabang')) {
            Schema::create('travel_cabang', function (Blueprint $table) {
                $table->id('id_cabang');
                $table->string('Penyelenggara');
                $table->string('SK_BA')->nullable();
                $table->date('tanggal')->nullable();
                $table->string('pimpinan_cabang');
                $table->text('alamat_cabang');
                $table->string('telepon');
                $table->string('kabupaten');
                $table->string('pusat')->nullable();
                $table->string('pimpinan_pusat');
                $table->text('alamat_pusat');
                $table->timestamps();
            });
        }

        // Create users table
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->foreignId('travel_id')->nullable()->constrained()->onDelete('cascade');
                $table->string('username');
                $table->string('firstname')->nullable();
                $table->string('lastname')->nullable();
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->enum('role', ['admin', 'kabupaten', 'user'])->default('user');
                $table->string('address')->nullable();
                $table->string('city')->nullable();
                $table->string('country')->nullable();
                $table->string('postal')->nullable();
                $table->text('about')->nullable();
                $table->boolean('is_password_changed')->default(0);
                $table->rememberToken();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengunduran');
        Schema::dropIfExists('pengaduan');
        Schema::dropIfExists('jamaah_haji_khusus');
        Schema::dropIfExists('jamaah');
        Schema::dropIfExists('bap');
        Schema::dropIfExists('sertifikat');
        Schema::dropIfExists('sertifikat_settings');
        Schema::dropIfExists('travel_cabang');
        Schema::dropIfExists('travels');
        Schema::dropIfExists('users');
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('failed_jobs');
    }
}; 