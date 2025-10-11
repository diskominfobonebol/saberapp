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
                $this->command->error('❌ Token tidak diterima dari API.');
                return;
            }
            $this->command->info('✅ Login API berhasil.');

            // --- 2️⃣ Ambil data pegawai ---
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

            $this->command->info('⏩ Skip pengosongan tabel. Data lama akan diperbarui bila sudah ada.');
            $this->command->info('🧹 Tabel users, pegawais, dan opds telah dikosongkan.');

            // --- 4️⃣ Pastikan Role Ada ---
            $rolePegawai = Role::firstOrCreate(['name' => 'pegawai']);
            $this->command->info('👤 Role "pegawai" siap digunakan.');

            // --- 5️⃣ Proses data ---
            $this->command->getOutput()->progressStart(count($allPegawaiData));

            foreach ($allPegawaiData as $pegawaiFromApi) {
                // 🔹 Konversi nama lengkap
                $gelarDepan = !empty($pegawaiFromApi['GELAR_DEPAN']) ? trim($pegawaiFromApi['GELAR_DEPAN']) . ' ' : '';
                $nama = trim($pegawaiFromApi['NAMA'] ?? '');
                $gelarBelakang = !empty($pegawaiFromApi['GELAR_BELAKANG']) ? ', ' . trim($pegawaiFromApi['GELAR_BELAKANG']) : '';
                $namaLengkap = $gelarDepan . $nama . $gelarBelakang;

                // 🔹 Simpan OPD (organisasi)
                $opd = Opd::updateOrCreate(
                    ['uuid' =>  Str::uuid()],
                    [
                        'nama_opd' => $pegawaiFromApi['opd_nama'] ?? 'Tidak diketahui',
                        'user_id' => null
                    ]
                );

                // 🔹 Simpan User
                $user = User::create([
                    'name' => $namaLengkap,
                    'email' => $pegawaiFromApi['NIP_BARU'] . '@example.com',
                    'nip' => $pegawaiFromApi['NIP_BARU'],
                    'username' => $pegawaiFromApi['NIP_BARU'],
                    'password' => Hash::make('12345678'),
                ]);

                // 🔹 Tambahkan Role Pegawai
                $user->assignRole($rolePegawai);

                // 🔹 Konversi tanggal
                $tanggalLahir = !empty($pegawaiFromApi['TANGGAL_LAHIR']) ? Carbon::parse($pegawaiFromApi['TANGGAL_LAHIR'])->format('Y-m-d') : null;
                $tmtGolongan = !empty($pegawaiFromApi['TMT_GOLONGAN']) ? Carbon::parse($pegawaiFromApi['TMT_GOLONGAN'])->format('Y-m-d') : null;
                $tmtJabatan = !empty($pegawaiFromApi['TMT_JABATAN']) ? Carbon::parse($pegawaiFromApi['TMT_JABATAN'])->format('Y-m-d') : null;

                // 🔹 Simpan Pegawai
                Pegawai::create([
                    'uuid' => Str::uuid(),
                    'nip' => $pegawaiFromApi['NIP_BARU'] ?? null,
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
                ]);

                $this->command->getOutput()->progressAdvance();
            }

            $this->command->getOutput()->progressFinish();
            $this->command->info('🚀 Seeder data pegawai & user selesai dijalankan!');
        } catch (Throwable $e) {
            $this->command->error('💥 Terjadi error: ' . $e->getMessage());
        }
    }
}
