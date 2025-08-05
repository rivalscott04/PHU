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
        // 1. Users table
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('travel_id')->nullable()->index('users_travel_id_foreign'); // index for FK
                $table->string('username');
                $table->string('firstname')->nullable();
                $table->string('lastname')->nullable();
                $table->string('email')->unique('users_email_unique');
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

        // 2. Password reset tokens
        if (!Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        // 3. Failed jobs
        if (!Schema::hasTable('failed_jobs')) {
            Schema::create('failed_jobs', function (Blueprint $table) {
                $table->id();
                $table->string('uuid')->unique('failed_jobs_uuid_unique');
                $table->text('connection');
                $table->text('queue');
                $table->longText('payload');
                $table->longText('exception');
                $table->timestamp('failed_at')->useCurrent();
            });
        }

        // 4. Personal access tokens
        if (!Schema::hasTable('personal_access_tokens')) {
            Schema::create('personal_access_tokens', function (Blueprint $table) {
                $table->id();
                $table->morphs('tokenable');
                $table->string('name');
                $table->string('token', 64)->unique('personal_access_tokens_token_unique');
                $table->text('abilities')->nullable();
                $table->timestamp('last_used_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
                $table->index(['tokenable_type', 'tokenable_id'], 'personal_access_tokens_tokenable_type_tokenable_id_index');
            });
        }

        // 5. Travels table
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

        // 6. Jamaah table
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

        // 7. BAP table
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
                $table->unsignedBigInteger('user_id')->index('bap_user_id_foreign'); // index for FK
                $table->timestamps();
            });
        }

        // 8. Travel Cabang table
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

        // 9. Pengaduan table
        if (!Schema::hasTable('pengaduan')) {
            Schema::create('pengaduan', function (Blueprint $table) {
                $table->id();
                $table->string('nama_pengadu');
                $table->unsignedBigInteger('travels_id')->index('pengaduan_travels_id_foreign'); // index for FK
                $table->text('hal_aduan');
                $table->string('berkas_aduan')->nullable();
                $table->string('status')->default('pending');
                $table->string('pdf_output')->nullable();
                $table->text('admin_notes')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->unsignedBigInteger('processed_by')->nullable()->index('pengaduan_processed_by_foreign'); // index for FK
                $table->timestamps();
            });
        }

        // 10. Pengunduran table
        if (!Schema::hasTable('pengunduran')) {
            Schema::create('pengunduran', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index('pengunduran_user_id_foreign'); // index for FK
                $table->string('berkas_pengunduran');
                $table->enum('status', ['pending', 'diajukan', 'diterima'])->default('pending');
                $table->timestamps();
            });
        }

        // 11. Jamaah Haji Khusus table
        if (!Schema::hasTable('jamaah_haji_khusus')) {
            Schema::create('jamaah_haji_khusus', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('travel_id')->index('jamaah_haji_khusus_travel_id_foreign'); // index for FK
                $table->string('nama_lengkap');
                $table->string('no_ktp', 16)->unique('jamaah_haji_khusus_no_ktp_unique');
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
                $table->unsignedBigInteger('verified_by')->nullable()->index('jamaah_haji_khusus_verified_by_foreign'); // index for FK
                $table->timestamps();
            });
        }

        // 12. Migrations table (Laravel's own table)
        if (!Schema::hasTable('migrations')) {
            Schema::create('migrations', function (Blueprint $table) {
                $table->id();
                $table->string('migration');
                $table->integer('batch');
            });
        }

        // Foreign key constraints
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'travel_id')) {
                $table->foreign('travel_id', 'users_travel_id_foreign')->references('id')->on('travels')->onDelete('cascade');
            }
        });
        Schema::table('bap', function (Blueprint $table) {
            if (Schema::hasColumn('bap', 'user_id')) {
                $table->foreign('user_id', 'bap_user_id_foreign')->references('id')->on('users');
            }
        });
        Schema::table('pengaduan', function (Blueprint $table) {
            if (Schema::hasColumn('pengaduan', 'travels_id')) {
                $table->foreign('travels_id', 'pengaduan_travels_id_foreign')->references('id')->on('travels')->onDelete('cascade');
            }
            if (Schema::hasColumn('pengaduan', 'processed_by')) {
                $table->foreign('processed_by', 'pengaduan_processed_by_foreign')->references('id')->on('users')->onDelete('set null');
            }
        });
        Schema::table('pengunduran', function (Blueprint $table) {
            if (Schema::hasColumn('pengunduran', 'user_id')) {
                $table->foreign('user_id', 'pengunduran_user_id_foreign')->references('id')->on('users')->onDelete('cascade');
            }
        });
        Schema::table('jamaah_haji_khusus', function (Blueprint $table) {
            if (Schema::hasColumn('jamaah_haji_khusus', 'travel_id')) {
                $table->foreign('travel_id', 'jamaah_haji_khusus_travel_id_foreign')->references('id')->on('travels')->onDelete('cascade');
            }
            if (Schema::hasColumn('jamaah_haji_khusus', 'verified_by')) {
                $table->foreign('verified_by', 'jamaah_haji_khusus_verified_by_foreign')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop tables in reverse order
        Schema::dropIfExists('migrations');
        Schema::dropIfExists('jamaah_haji_khusus');
        Schema::dropIfExists('pengunduran');
        Schema::dropIfExists('pengaduan');
        Schema::dropIfExists('travel_cabang');
        Schema::dropIfExists('bap');
        Schema::dropIfExists('jamaah');
        Schema::dropIfExists('travels');
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
