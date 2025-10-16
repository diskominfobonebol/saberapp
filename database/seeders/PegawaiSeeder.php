<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;
use App\Models\Opd;
use Illuminate\Support\Str;
use Throwable;

class PegawaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            // --- 1️⃣ Login API ---
            $this->command->info('🔐 Login ke API...');
            $loginResponse = Http::post('https://api-bkpsdm.bonebolangokab.go.id/auth/login', [
                'username' => env('API_USERNAME', 'kominfo'),
                'password' => env('API_PASSWORD', 'P3w4r15@bkpsdm25'),
            ]);

            if ($loginResponse->failed()) {
                $this->command->error('❌ Gagal login ke API. Cek kredensial atau koneksi.');
                return;
            }

            $token = $loginResponse->json('access_token');
            if (!$token) {
                $this->command->error(' Token tidak diterima dari API.');
                return;
            }
            $this->command->info('✅ Login API berhasil.');

            // --- 2️ Ambil data pegawai ---
            $this->command->info('📦 Mengambil data pegawai dari API...');
            $pegawaiResponse = Http::withToken($token)
                ->timeout(180)
                ->get('https://api-bkpsdm.bonebolangokab.go.id/asn?jenis=all');

            if ($pegawaiResponse->failed()) {
                $this->command->error('❌ Gagal mengambil data pegawai. Kode status: ' . $pegawaiResponse->status());
                return;
            }

            $allPegawaiData = $pegawaiResponse->json();
            if (empty($allPegawaiData) || !is_array($allPegawaiData)) {
                $this->command->warn('⚠️ Tidak ada data pegawai yang ditemukan.');
                return;
            }

            $this->command->info('✅ Berhasil mengambil ' . count($allPegawaiData) . ' data pegawai.');

            // --- 3️⃣ Persiapan Database ---
            $this->command->info('⏩ Data lama akan diperbarui jika sudah ada berdasarkan NIP.');

            // --- 4️⃣ Pastikan Role Ada ---
            $rolePegawai = Role::firstOrCreate(['name' => 'pegawai']);
            $this->command->info('👤 Role "pegawai" siap digunakan.');

            // --- 5️⃣ Proses data ---
            $this->command->getOutput()->progressStart(count($allPegawaiData));

            foreach ($allPegawaiData as $pegawaiFromApi) {
                // Skip jika NIP tidak ada
                if (empty($pegawaiFromApi['NIP_BARU'])) {
                    $this->command->getOutput()->progressAdvance();
                    continue;
                }

                // 🔹 Konversi nama lengkap
                $gelarDepan = !empty($pegawaiFromApi['GELAR_DEPAN']) ? trim($pegawaiFromApi['GELAR_DEPAN']) . ' ' : '';
                $nama = trim($pegawaiFromApi['NAMA'] ?? '');
                $gelarBelakang = !empty($pegawaiFromApi['GELAR_BELAKANG']) ? ', ' . trim($pegawaiFromApi['GELAR_BELAKANG']) : '';
                $namaLengkap = $gelarDepan . $nama . $gelarBelakang;

                // 🔹 Simpan OPD (organisasi) - DIUBAH
                $opd = Opd::firstOrCreate(
                    ['nama_opd' => $pegawaiFromApi['opd_nama'] ?? 'Tidak Diketahui'],
                    ['uuid' => Str::uuid()]
                );

                // 🔹 Simpan User - DIUBAH
                $user = User::updateOrCreate(
                    ['nip' => $pegawaiFromApi['NIP_BARU']],
                    [
                        'name' => $namaLengkap,
                        'email' => $pegawaiFromApi['NIP_BARU'] . '@example.com',
                        'username' => $pegawaiFromApi['NIP_BARU'],
                        'password' => Hash::make('12345678'),
                    ]
                );

                // 🔹 Tambahkan Role Pegawai
                $user->assignRole($rolePegawai);

                // 🔹 Konversi tanggal
                $tanggalLahir = !empty($pegawaiFromApi['TANGGAL_LAHIR']) ? Carbon::parse($pegawaiFromApi['TANGGAL_LAHIR'])->format('Y-m-d') : null;
                $tmtGolongan = !empty($pegawaiFromApi['TMT_GOLONGAN']) ? Carbon::parse($pegawaiFromApi['TMT_GOLONGAN'])->format('Y-m-d') : null;
                $tmtJabatan = !empty($pegawaiFromApi['TMT_JABATAN']) ? Carbon::parse($pegawaiFromApi['TMT_JABATAN'])->format('Y-m-d') : null;

                // 🔹 Simpan Pegawai - DIUBAH
                Pegawai::updateOrCreate(
                    ['nip' => $pegawaiFromApi['NIP_BARU']],
                    [
                        // 'uuid' sebaiknya di-handle oleh model saat pembuatan
                        'user_id' => $user->id,
                        'nama' => $namaLengkap,
                        'gelar_depan' => $pegawaiFromApi['GELAR_DEPAN'] ?? null,
                        'gelar_belakang' => $pegawaiFromApi['GELAR_BELAKANG'] ?? null,
                        'tempat_lahir' => $pegawaiFromApi['TEMPAT_LAHIR_NAMA'] ?? null,
                        'tanggal_lahir' => $tanggalLahir,
                        'jenis_kelamin' => $pegawaiFromApi['JENIS_KELAMIN'] ?? null,
                        'agama' => $pegawaiFromApi['AGAMA_NAMA'] ?? null,
                        'status_perkawinan' => $pegawaiFromApi['JENIS_KAWIN_NAMA'] ?? null,
                        'nomor_hp' => $pegawaiFromApi['NOMOR_HP'] ?? null,
                        'alamat' => $pegawaiFromApi['ALAMAT'] ?? null,
                        'jenis_pegawai' => $pegawaiFromApi['JENIS_PEGAWAI_NAMA'] ?? null,
                        'golongan' => $pegawaiFromApi['GOL_AKHIR_NAMA'] ?? null,
                        'tmt_golongan' => $tmtGolongan,
                        'jenis_jabatan' => $pegawaiFromApi['JENIS_JABATAN_NAMA'] ?? null,
                        'jabatan' => $pegawaiFromApi['JABATAN_NAMA'] ?? null,
                        'tmt_jabatan' => $tmtJabatan,
                        'pendidikan_terakhir' => $pegawaiFromApi['TINGKAT_PENDIDIKAN_NAMA'] ?? null,
                        'tahun_lulus' => $pegawaiFromApi['TAHUN_LULUS'] ?? null,
                        'opd_id' => $opd->id,
                    ]
                );

                $this->command->getOutput()->progressAdvance();
            }

            $this->command->getOutput()->progressFinish();
            $this->command->info("\n🚀 Seeder data pegawai & user selesai dijalankan!");
        } catch (Throwable $e) {
            $this->command->error('💥 Terjadi error: ' . $e->getMessage() . ' di baris ' . $e->getLine());
        }
    }
}
