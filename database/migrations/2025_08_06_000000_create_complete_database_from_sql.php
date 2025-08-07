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
        // STEP 1: Create all tables first without foreign keys
        
        // Create users table
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('travel_id')->nullable();
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
                $table->unsignedBigInteger('user_id');
                $table->timestamps();
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
                $table->unsignedBigInteger('travel_id');
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
                $table->unsignedBigInteger('verified_by')->nullable();
                $table->timestamps();
            });
        }

        // Create jamaah_umrah table
        if (!Schema::hasTable('jamaah_umrah')) {
            Schema::create('jamaah_umrah', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('travel_id');
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
                $table->string('golongan_darah');
                $table->string('alergi')->nullable();
                $table->string('no_paspor')->nullable();
                $table->date('tanggal_berlaku_paspor')->nullable();
                $table->string('tempat_terbit_paspor')->nullable();
                $table->enum('status_pendaftaran', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
                $table->text('catatan_khusus')->nullable();
                $table->string('dokumen_ktp')->nullable();
                $table->string('dokumen_kk')->nullable();
                $table->string('dokumen_paspor')->nullable();
                $table->string('dokumen_foto')->nullable();
                $table->string('surat_keterangan')->nullable();
                $table->string('bukti_setor_bank')->nullable();
                $table->enum('status_verifikasi_bukti', ['pending', 'verified', 'rejected'])->default('pending');
                $table->unsignedBigInteger('processed_by')->nullable();
                $table->timestamps();
            });
        }

        // Create pengunduran table
        if (!Schema::hasTable('pengunduran')) {
            Schema::create('pengunduran', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('travel_id');
                $table->string('nama_jamaah');
                $table->string('no_ktp', 16);
                $table->string('no_paspor')->nullable();
                $table->string('jenis_pengunduran');
                $table->text('alasan_pengunduran');
                $table->string('dokumen_pendukung')->nullable();
                $table->enum('status_pengunduran', ['pending', 'approved', 'rejected'])->default('pending');
                $table->text('catatan_admin')->nullable();
                $table->timestamps();
            });
        }

        // Create sertifikat table
        if (!Schema::hasTable('sertifikat')) {
            Schema::create('sertifikat', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->unique();
                $table->unsignedBigInteger('travel_id');
                $table->unsignedBigInteger('cabang_id')->nullable();
                $table->string('nama_ppiu');
                $table->string('nama_kepala');
                $table->text('alamat');
                $table->date('tanggal_diterbitkan');
                $table->date('tanggal_tandatangan');
                $table->string('nomor_surat');
                $table->string('nomor_dokumen');
                $table->string('qrcode_path')->nullable();
                $table->string('sertifikat_path')->nullable();
                $table->string('pdf_path')->nullable();
                $table->enum('jenis', ['PPIU'])->default('PPIU');
                $table->enum('jenis_lokasi', ['pusat', 'cabang'])->default('pusat');
                $table->enum('status', ['active', 'revoked'])->default('active');
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

        // Create pengaduan table
        if (!Schema::hasTable('pengaduan')) {
            Schema::create('pengaduan', function (Blueprint $table) {
                $table->id();
                $table->string('nama_pelapor');
                $table->string('email_pelapor');
                $table->string('telepon_pelapor');
                $table->string('kabupaten_pelapor');
                $table->string('judul_pengaduan');
                $table->text('deskripsi_pengaduan');
                $table->string('kategori_pengaduan');
                $table->string('nama_travel')->nullable();
                $table->string('status_pengaduan')->default('pending');
                $table->text('tanggapan_pengaduan')->nullable();
                $table->string('file_lampiran')->nullable();
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

        // Create password_reset_tokens table
        if (!Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
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

        // STEP 2: Add foreign keys and constraints after all tables are created
        
        // Add foreign key constraints
        if (!Schema::hasColumn('users', 'travel_id') || !Schema::hasTable('travels')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreign('travel_id')->references('id')->on('travels')->onDelete('cascade');
            });
        }

        if (!Schema::hasColumn('bap', 'user_id') || !Schema::hasTable('users')) {
            Schema::table('bap', function (Blueprint $table) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        if (!Schema::hasColumn('jamaah_haji_khusus', 'travel_id') || !Schema::hasTable('travels')) {
            Schema::table('jamaah_haji_khusus', function (Blueprint $table) {
                $table->foreign('travel_id')->references('id')->on('travels')->onDelete('cascade');
            });
        }

        if (!Schema::hasColumn('jamaah_haji_khusus', 'verified_by') || !Schema::hasTable('users')) {
            Schema::table('jamaah_haji_khusus', function (Blueprint $table) {
                $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');
            });
        }

        if (!Schema::hasColumn('jamaah_umrah', 'travel_id') || !Schema::hasTable('travels')) {
            Schema::table('jamaah_umrah', function (Blueprint $table) {
                $table->foreign('travel_id')->references('id')->on('travels')->onDelete('cascade');
            });
        }

        if (!Schema::hasColumn('jamaah_umrah', 'processed_by') || !Schema::hasTable('users')) {
            Schema::table('jamaah_umrah', function (Blueprint $table) {
                $table->foreign('processed_by')->references('id')->on('users')->onDelete('set null');
            });
        }

        if (!Schema::hasColumn('pengunduran', 'travel_id') || !Schema::hasTable('travels')) {
            Schema::table('pengunduran', function (Blueprint $table) {
                $table->foreign('travel_id')->references('id')->on('travels')->onDelete('cascade');
            });
        }

        if (!Schema::hasColumn('sertifikat', 'travel_id') || !Schema::hasTable('travels')) {
            Schema::table('sertifikat', function (Blueprint $table) {
                $table->foreign('travel_id')->references('id')->on('travels')->onDelete('cascade');
            });
        }

        if (!Schema::hasColumn('sertifikat', 'cabang_id') || !Schema::hasTable('travel_cabang')) {
            Schema::table('sertifikat', function (Blueprint $table) {
                $table->foreign('cabang_id')->references('id_cabang')->on('travel_cabang')->onDelete('cascade');
            });
        }

        // STEP 3: Add indexes for better performance
        Schema::table('users', function (Blueprint $table) {
            $table->index('travel_id');
            $table->index('role');
        });

        Schema::table('bap', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('status');
            $table->index('datetime');
        });

        Schema::table('jamaah_haji_khusus', function (Blueprint $table) {
            $table->index('travel_id');
            $table->index('status_pendaftaran');
            $table->index('status_verifikasi_bukti');
            $table->index('verified_by');
        });

        Schema::table('jamaah_umrah', function (Blueprint $table) {
            $table->index('travel_id');
            $table->index('status_pendaftaran');
            $table->index('status_verifikasi_bukti');
            $table->index('processed_by');
        });

        Schema::table('pengunduran', function (Blueprint $table) {
            $table->index('travel_id');
            $table->index('status_pengunduran');
        });

        Schema::table('sertifikat', function (Blueprint $table) {
            $table->index('travel_id');
            $table->index('cabang_id');
            $table->index('jenis');
            $table->index('status');
        });

        Schema::table('pengaduan', function (Blueprint $table) {
            $table->index('status_pengaduan');
            $table->index('kategori_pengaduan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign keys first
        Schema::table('sertifikat', function (Blueprint $table) {
            $table->dropForeign(['travel_id', 'cabang_id']);
        });

        Schema::table('pengunduran', function (Blueprint $table) {
            $table->dropForeign(['travel_id']);
        });

        Schema::table('jamaah_umrah', function (Blueprint $table) {
            $table->dropForeign(['travel_id', 'processed_by']);
        });

        Schema::table('jamaah_haji_khusus', function (Blueprint $table) {
            $table->dropForeign(['travel_id', 'verified_by']);
        });

        Schema::table('bap', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['travel_id']);
        });

        // Drop tables in reverse order
        Schema::dropIfExists('pengunduran');
        Schema::dropIfExists('pengaduan');
        Schema::dropIfExists('jamaah_haji_khusus');
        Schema::dropIfExists('jamaah_umrah');
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