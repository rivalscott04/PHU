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
                $table->string('kabupaten')->nullable(); // Added from seeder
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

        // Create travels table (renamed from travels to travel_companies based on seeder usage)
        if (!Schema::hasTable('travels')) {
            Schema::create('travels', function (Blueprint $table) {
                $table->id();
                $table->string('Penyelenggara');
                $table->string('Status');
                $table->string('kab_kota'); // Kabupaten/Kota travel berada
                $table->string('alamat')->nullable(); // Made nullable
                $table->string('no_telp')->nullable(); // Made nullable
                $table->string('email')->nullable(); // Made nullable
                $table->string('website')->nullable();
                $table->string('nama_pimpinan')->nullable(); // Made nullable
                $table->string('jabatan_pimpinan')->nullable(); // Made nullable
                $table->string('no_izin')->nullable(); // Made nullable
                $table->date('tanggal_izin')->nullable(); // Made nullable
                $table->string('masa_berlaku')->nullable();
                $table->string('kantor_cabang')->nullable();

                // Additional fields from seeder
                $table->string('Pusat')->nullable(); // From seeder
                $table->date('Tanggal')->nullable(); // From seeder
                $table->string('nilai_akreditasi')->nullable(); // From seeder
                $table->date('tanggal_akreditasi')->nullable(); // From seeder
                $table->string('lembaga_akreditasi')->nullable(); // From seeder
                $table->string('Pimpinan')->nullable(); // From seeder
                $table->text('alamat_kantor_lama')->nullable(); // From seeder
                $table->text('alamat_kantor_baru')->nullable(); // From seeder
                $table->string('Telepon')->nullable(); // From seeder
                $table->boolean('can_haji')->default(false); // From seeder
                $table->boolean('can_umrah')->default(false); // From seeder
                $table->json('capabilities')->nullable(); // From seeder

                $table->timestamps();
            });
        }

        // Create travel_cabang table
        if (!Schema::hasTable('travel_cabang')) {
            Schema::create('travel_cabang', function (Blueprint $table) {
                $table->id('id_cabang');
                $table->unsignedBigInteger('travel_id')->nullable(); // Made nullable for seeder compatibility
                $table->string('nama_cabang')->nullable(); // Made nullable
                $table->string('alamat_cabang')->nullable(); // Made nullable
                $table->string('no_telp_cabang')->nullable(); // Made nullable
                $table->string('email_cabang')->nullable();
                $table->string('nama_pimpinan_cabang')->nullable(); // Made nullable
                $table->string('jabatan_pimpinan_cabang')->nullable(); // Made nullable

                // Fields that are actually used in CabangTravelSeeder
                $table->string('Penyelenggara')->nullable(); // From seeder error
                $table->string('kabupaten')->nullable(); // From seeder
                $table->string('pusat')->nullable(); // From seeder
                $table->string('pimpinan_pusat')->nullable(); // From seeder
                $table->text('alamat_pusat')->nullable(); // From seeder
                $table->string('SK_BA')->nullable(); // From seeder
                $table->date('tanggal')->nullable(); // From seeder
                $table->string('pimpinan_cabang')->nullable(); // From seeder
                $table->string('telepon')->nullable(); // From seeder

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
                $table->string('nomor_surat')->nullable(); // Added from seeder
                $table->timestamps();
            });
        }

        // Create jamaah table (for Umrah and Haji)
        if (!Schema::hasTable('jamaah')) {
            Schema::create('jamaah', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('travel_id'); // Jamaah ikut travel
                $table->unsignedBigInteger('user_id')->nullable(); // Made nullable since seeder doesn't always set it
                $table->string('nik', 16); // No unique constraint - one person can go multiple times
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
                $table->unsignedBigInteger('travel_id'); // Jamaah ikut travel
                $table->string('nama_lengkap');
                $table->string('no_ktp', 16); // No unique constraint - one person can go multiple times
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
                $table->enum('pergi_haji', ['Belum', 'Sudah']);
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
                $table->enum('status_verifikasi_bukti', ['verified', 'rejected'])->nullable();
                $table->text('catatan_verifikasi')->nullable();
                $table->timestamp('tanggal_verifikasi')->nullable();
                $table->unsignedBigInteger('verified_by')->nullable();
                $table->timestamps();
            });
        }

        // Create pengaduan table
        if (!Schema::hasTable('pengaduan')) {
            Schema::create('pengaduan', function (Blueprint $table) {
                $table->id();
                $table->string('nama_pelapor');
                $table->string('email_pelapor');
                $table->string('no_hp_pelapor');
                $table->enum('kategori_pengaduan', ['pelayanan', 'administrasi', 'teknis', 'lainnya']);
                $table->string('judul_pengaduan');
                $table->text('isi_pengaduan');
                $table->string('berkas_aduan')->nullable();
                $table->enum('status_pengaduan', ['pending', 'diproses', 'selesai', 'ditolak'])->default('pending');
                $table->text('tanggapan_admin')->nullable();
                $table->string('pdf_output')->nullable();
                $table->text('admin_notes')->nullable();
                $table->timestamp('completed_at')->nullable();
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
                $table->string('jenis_perjalanan');
                $table->text('alasan_pengunduran');
                $table->enum('status_pengunduran', ['pending', 'approved', 'rejected'])->default('pending');
                $table->text('catatan_admin')->nullable();
                $table->timestamps();
            });
        }

        // Create sertifikat table
        if (!Schema::hasTable('sertifikat')) {
            Schema::create('sertifikat', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('travel_id');
                $table->unsignedBigInteger('cabang_id')->nullable();
                $table->enum('jenis', ['umrah', 'haji', 'haji_khusus']);
                $table->string('nomor_sertifikat')->unique();
                $table->date('tanggal_terbit');
                $table->date('tanggal_berlaku');
                $table->enum('status', ['aktif', 'nonaktif', 'expired'])->default('aktif');
                $table->string('file_path')->nullable();
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

        // Add foreign key constraints
        $this->addForeignKeys();

        // Add indexes safely
        $this->addIndexes();
    }

    /**
     * Add foreign key constraints safely
     */
    private function addForeignKeys()
    {
        // Users -> Travels
        if (Schema::hasTable('users') && Schema::hasTable('travels')) {
            try {
                Schema::table('users', function (Blueprint $table) {
                    $table->foreign('travel_id')->references('id')->on('travels')->onDelete('set null');
                });
            } catch (\Exception $e) {
                // Foreign key might already exist
            }
        }

        // Travel Cabang -> Travels
        if (Schema::hasTable('travel_cabang') && Schema::hasTable('travels')) {
            try {
                Schema::table('travel_cabang', function (Blueprint $table) {
                    $table->foreign('travel_id')->references('id')->on('travels')->onDelete('cascade');
                });
            } catch (\Exception $e) {
                // Foreign key might already exist
            }
        }

        // BAP -> Users
        if (Schema::hasTable('bap') && Schema::hasTable('users')) {
            try {
                Schema::table('bap', function (Blueprint $table) {
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                });
            } catch (\Exception $e) {
                // Foreign key might already exist
            }
        }

        // Jamaah -> Travels & Users
        if (Schema::hasTable('jamaah') && Schema::hasTable('travels') && Schema::hasTable('users')) {
            try {
                Schema::table('jamaah', function (Blueprint $table) {
                    $table->foreign('travel_id')->references('id')->on('travels')->onDelete('cascade');
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
                });
            } catch (\Exception $e) {
                // Foreign key might already exist
            }
        }

        // Jamaah Haji Khusus -> Travels & Users
        if (Schema::hasTable('jamaah_haji_khusus') && Schema::hasTable('travels') && Schema::hasTable('users')) {
            try {
                Schema::table('jamaah_haji_khusus', function (Blueprint $table) {
                    $table->foreign('travel_id')->references('id')->on('travels')->onDelete('cascade');
                    $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');
                });
            } catch (\Exception $e) {
                // Foreign key might already exist
            }
        }

        // Pengaduan -> Users
        if (Schema::hasTable('pengaduan') && Schema::hasTable('users')) {
            try {
                Schema::table('pengaduan', function (Blueprint $table) {
                    $table->foreign('processed_by')->references('id')->on('users')->onDelete('set null');
                });
            } catch (\Exception $e) {
                // Foreign key might already exist
            }
        }

        // Pengunduran -> Travels
        if (Schema::hasTable('pengunduran') && Schema::hasTable('travels')) {
            try {
                Schema::table('pengunduran', function (Blueprint $table) {
                    $table->foreign('travel_id')->references('id')->on('travels')->onDelete('cascade');
                });
            } catch (\Exception $e) {
                // Foreign key might already exist
            }
        }

        // Sertifikat -> Travels & Travel Cabang
        if (Schema::hasTable('sertifikat') && Schema::hasTable('travels') && Schema::hasTable('travel_cabang')) {
            try {
                Schema::table('sertifikat', function (Blueprint $table) {
                    $table->foreign('travel_id')->references('id')->on('travels')->onDelete('cascade');
                    $table->foreign('cabang_id')->references('id_cabang')->on('travel_cabang')->onDelete('set null');
                });
            } catch (\Exception $e) {
                // Foreign key might already exist
            }
        }
    }

    /**
     * Add indexes safely
     */
    private function addIndexes()
    {
        // Users indexes
        if (Schema::hasTable('users')) {
            try {
                Schema::table('users', function (Blueprint $table) {
                    $table->index('travel_id');
                    $table->index('role');
                    $table->index('kabupaten');
                });
            } catch (\Exception $e) {
                // Index might already exist
            }
        }

        // Travels indexes - untuk filtering berdasarkan kabupaten/kota
        if (Schema::hasTable('travels')) {
            try {
                Schema::table('travels', function (Blueprint $table) {
                    $table->index('kab_kota'); // Untuk filtering role kabupaten
                    $table->index('Status'); // Untuk filtering PPIU/PIHK
                });
            } catch (\Exception $e) {
                // Index might already exist
            }
        }

        // BAP indexes
        if (Schema::hasTable('bap')) {
            try {
                Schema::table('bap', function (Blueprint $table) {
                    $table->index('user_id');
                    $table->index('status');
                    $table->index('datetime');
                });
            } catch (\Exception $e) {
                // Index might already exist
            }
        }

        // Jamaah Haji Khusus indexes
        if (Schema::hasTable('jamaah_haji_khusus')) {
            try {
                Schema::table('jamaah_haji_khusus', function (Blueprint $table) {
                    $table->index('travel_id');
                    $table->index('status_pendaftaran');
                    $table->index('status_verifikasi_bukti');
                    $table->index('verified_by');
                });
            } catch (\Exception $e) {
                // Index might already exist
            }
        }

        // Jamaah indexes
        if (Schema::hasTable('jamaah')) {
            try {
                Schema::table('jamaah', function (Blueprint $table) {
                    $table->index('travel_id');
                    $table->index('user_id');
                    $table->index('jenis_jamaah');
                });
            } catch (\Exception $e) {
                // Index might already exist
            }
        }

        // Pengunduran indexes
        if (Schema::hasTable('pengunduran')) {
            try {
                Schema::table('pengunduran', function (Blueprint $table) {
                    $table->index('travel_id');
                    $table->index('status_pengunduran');
                });
            } catch (\Exception $e) {
                // Index might already exist
            }
        }

        // Sertifikat indexes
        if (Schema::hasTable('sertifikat')) {
            try {
                Schema::table('sertifikat', function (Blueprint $table) {
                    $table->index('travel_id');
                    $table->index('cabang_id');
                    $table->index('jenis');
                    $table->index('status');
                });
            } catch (\Exception $e) {
                // Index might already exist
            }
        }

        // Pengaduan indexes
        if (Schema::hasTable('pengaduan')) {
            try {
                Schema::table('pengaduan', function (Blueprint $table) {
                    $table->index('status_pengaduan');
                    $table->index('kategori_pengaduan');
                });
            } catch (\Exception $e) {
                // Index might already exist
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign keys first
        if (Schema::hasTable('sertifikat')) {
            Schema::table('sertifikat', function (Blueprint $table) {
                $table->dropForeign(['cabang_id']);
                $table->dropForeign(['travel_id']);
            });
        }

        if (Schema::hasTable('pengunduran')) {
            Schema::table('pengunduran', function (Blueprint $table) {
                $table->dropForeign(['travel_id']);
            });
        }

        if (Schema::hasTable('pengaduan')) {
            Schema::table('pengaduan', function (Blueprint $table) {
                $table->dropForeign(['processed_by']);
            });
        }

        if (Schema::hasTable('jamaah_haji_khusus')) {
            Schema::table('jamaah_haji_khusus', function (Blueprint $table) {
                $table->dropForeign(['verified_by']);
                $table->dropForeign(['travel_id']);
            });
        }

        if (Schema::hasTable('jamaah')) {
            Schema::table('jamaah', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
                $table->dropForeign(['travel_id']);
            });
        }

        if (Schema::hasTable('bap')) {
            Schema::table('bap', function (Blueprint $table) {
                $table->dropForeign(['user_id']);
            });
        }

        if (Schema::hasTable('travel_cabang')) {
            Schema::table('travel_cabang', function (Blueprint $table) {
                $table->dropForeign(['travel_id']);
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['travel_id']);
            });
        }

        // Drop tables in reverse dependency order
        Schema::dropIfExists('sertifikat');
        Schema::dropIfExists('pengunduran');
        Schema::dropIfExists('pengaduan');
        Schema::dropIfExists('jamaah_haji_khusus');
        Schema::dropIfExists('jamaah');
        Schema::dropIfExists('bap');
        Schema::dropIfExists('travel_cabang');
        Schema::dropIfExists('travels');
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('personal_access_tokens');
    }
};
