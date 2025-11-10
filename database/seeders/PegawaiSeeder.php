<?php

namespace Database\Seeders;

use App\Models\Pegawai;
use App\Models\User; // Dibiarkan jika model Pegawai punya relasi
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role; // Dihidupkan kembali
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
            // --- 1️⃣ Login API (SPLP BARU) ---
            $this->command->info('🔐 Login ke API SPLP...');
            $loginResponse = Http::post('https://splp.bonebolangokab.go.id/api/login', [
                'email' => env('SPLP_API_EMAIL', 'user@user.com'),
                'password' => env('SPLP_API_PASSWORD', 'password'),
            ]);

            if ($loginResponse->failed()) {
                $this->command->error('❌ Gagal login ke API SPLP. Cek kredensial (.env: SPLP_API_EMAIL/PASSWORD) atau koneksi.');
                return;
            }

            $token = $loginResponse->json('token');
            if (!$token) {
                $this->command->error(' Token tidak diterima dari API SPLP.');
                return;
            }
            $this->command->info('✅ Login API SPLP berhasil.');

            // --- 2️ Ambil data pegawai (SPLP BARU) ---
            $this->command->info('📦 Mengambil data pegawai dari API...');
            $pegawaiResponse = Http::withToken($token)
                ->timeout(180)
                ->get('https://splp.bonebolangokab.go.id/api/pegawai');

            if ($pegawaiResponse->failed()) {
                $this->command->error('❌ Gagal mengambil data pegawai. Kode status: ' . $pegawaiResponse->status());
                return;
            }

            $allPegawaiData = $pegawaiResponse->json('data');
            if (empty($allPegawaiData) || !is_array($allPegawaiData)) {
                $this->command->warn('⚠️ Tidak ada data pegawai yang ditemukan.');
                return;
            }

            $this->command->info('✅ Berhasil mengambil ' . count($allPegawaiData) . ' data pegawai.');

            // --- 3️⃣ Persiapan Database ---
            $this->command->info('⏩ Data lama akan diperbarui jika sudah ada berdasarkan UUID.');

            // --- 4️⃣ (DIKEMBALIKAN) Pastikan Role Ada ---
            $rolePegawai = Role::firstOrCreate(['name' => 'pegawai']);
            $this->command->info('👤 Role "pegawai" siap digunakan.');

            // --- 5️⃣ Proses data ---
            $this->command->getOutput()->progressStart(count($allPegawaiData));

            foreach ($allPegawaiData as $pegawaiFromApi) {
                // Skip jika NIP, UUID, atau data User tidak ada
                if (empty($pegawaiFromApi['nip']) || empty($pegawaiFromApi['uuid']) || empty($pegawaiFromApi['user'])) {
                    $this->command->warn('⚠️ Skipping data (NIP: ' . ($pegawaiFromApi['nip'] ?? 'N/A') . ') karena data tidak lengkap di API.');
                    $this->command->getOutput()->progressAdvance();
                    continue;
                }

                $userFromApi = $pegawaiFromApi['user'];

                // 🔹 Simpan OPD (organisasi)
                $opd = Opd::updateOrCreate(
                    ['uuid' => $pegawaiFromApi['opd']['uuid'] ?? Str::uuid()],
                    ['nama_opd' => $pegawaiFromApi['opd']['nama_opd'] ?? 'Tidak Diketahui']
                );

                // 🔹 (DIKEMBALIKAN) Simpan User
                $user = User::updateOrCreate(
                    ['nip' => $userFromApi['nip']], // Kunci unik NIP
                    [
                        'name' => $userFromApi['name'],
                        'email' => $userFromApi['email'],
                        'username' => $userFromApi['username'],
                        'password' => Hash::make('12345678'), // Default password
                    ]
                );

                // 🔹 (DIKEMBALIKAN) Tambahkan Role Pegawai
                $user->assignRole($rolePegawai);

                // 🔹 Konversi tanggal
                $tanggalLahir = !empty($pegawaiFromApi['tanggal_lahir']) ? Carbon::parse($pegawaiFromApi['tanggal_lahir'])->format('Y-m-d') : null;
                $tmtGolongan = !empty($pegawaiFromApi['tmt_golongan']) ? Carbon::parse($pegawaiFromApi['tmt_golongan'])->format('Y-m-d') : null;
                $tmtJabatan = !empty($pegawaiFromApi['tmt_jabatan']) ? Carbon::parse($pegawaiFromApi['tmt_jabatan'])->format('Y-m-d') : null;

                // 🔹 Simpan Pegawai
                Pegawai::updateOrCreate(
                    ['uuid' => $pegawaiFromApi['uuid']], // Kunci utama
                    [
                        'nip' => $pegawaiFromApi['nip'],
                        'user_id' => $user->id, // <-- (DIKEMBALIKAN) Hubungkan ke user
                        'nama' => $pegawaiFromApi['nama'],
                        'gelar_depan' => $pegawaiFromApi['gelar_depan'] ?? null,
                        'gelar_belakang' => $pegawaiFromApi['gelar_belakang'] ?? null,
                        'tempat_lahir' => $pegawaiFromApi['tempat_lahir'] ?? null,
                        'tanggal_lahir' => $tanggalLahir,
                        'jenis_kelamin' => $pegawaiFromApi['jenis_kelamin'] ?? null,
                        'agama' => $pegawaiFromApi['agama'] ?? null,
                        'status_perkawinan' => $pegawaiFromApi['status_perkawinan'] ?? null,
                        'nomor_hp' => $pegawaiFromApi['nomor_hp'] ?? null,
                        'alamat' => $pegawaiFromApi['alamat'] ?? null,
                        'jenis_pegawai' => $pegawaiFromApi['jenis_pegawai'] ?? null,
                        'golongan' => $pegawaiFromApi['golongan'] ?? null,
                        'tmt_golongan' => $tmtGolongan,
                        'jenis_jabatan' => $pegawaiFromApi['jenis_jabatan'] ?? null,
                        'jabatan' => $pegawaiFromApi['jabatan'] ?? null,
                        'tmt_jabatan' => $tmtJabatan,
                        'pendidikan_terakhir' => $pegawaiFromApi['pendidikan_terakhir'] ?? null,
                        'tahun_lulus' => $pegawaiFromApi['tahun_lulus'] ?? null,
                        'opd_id' => $opd->id,
                    ]
                );

                $this->command->getOutput()->progressAdvance();
            }

            $this->command->getOutput()->progressFinish();
            $this->command->info("\n🚀 Seeder data pegawai, user, & opd selesai dijalankan!"); // Pesan diupdate
        } catch (Throwable $e) {
            $this->command->error('💥 Terjadi error: ' . $e->getMessage() . ' di baris ' . $e->getLine());
        }
    }
}
