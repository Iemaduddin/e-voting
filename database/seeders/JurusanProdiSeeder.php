<?php

namespace Database\Seeders;

use App\Models\Jurusan;
use App\Models\Prodi;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JurusanProdiSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $dataJurusan = [
            [
                'nama' => 'Jurusan Teknologi Informasi',
                'kode_jurusan' => 'TI'
            ],
            [
                'nama' => 'Jurusan Teknik Mesin',
                'kode_jurusan' => 'TM'
            ],
            [
                'nama' => 'Jurusan Teknik Elektro',
                'kode_jurusan' => 'TE'
            ],
            [
                'nama' => 'Jurusan Teknik Sipil',
                'kode_jurusan' => 'TS'
            ],
            [
                'nama' => 'Jurusan Teknik Kimia',
                'kode_jurusan' => 'TK'
            ],
            [
                'nama' => 'Jurusan Administrasi Niaga',
                'kode_jurusan' => 'AN'
            ],
            [
                'nama' => 'Jurusan Akuntansi',
                'kode_jurusan' => 'AK'
            ],
        ];

        foreach ($dataJurusan as $jurusan) {
            Jurusan::create([
                'nama' => $jurusan['nama'],
                'kode_jurusan' => $jurusan['kode_jurusan'],
            ]);
        }
        $dataProdi = [
            // TI
            [
                'nama_prodi' => 'S2 Rekayasa Teknologi Informasi',
                'kode_prodi' => 'S2-RTI',
                'kode_jurusan' => 'TI'
            ],
            [
                'nama_prodi' => 'D4 Teknik Informatika',
                'kode_prodi' => 'D4-TI',
                'kode_jurusan' => 'TI'
            ],
            [
                'nama_prodi' => 'D4 Sistem Informasi Bisnis',
                'kode_prodi' => 'D4-SIB',
                'kode_jurusan' => 'TI'
            ],
            // TM
            [
                'nama_prodi' => 'S2 Rekayasa Teknologi Manufaktur',
                'kode_prodi' => 'S2-RTM',
                'kode_jurusan' => 'TM'
            ],
            [
                'nama_prodi' => 'D4 Teknik Otomotif Elektronik',
                'kode_prodi' => 'D4-TOE',
                'kode_jurusan' => 'TM'
            ],
            [
                'nama_prodi' => 'D4 Teknik Mesin Produksi dan Perawatan',
                'kode_prodi' => 'D4-TMPP',
                'kode_jurusan' => 'TM'
            ],
            [
                'nama_prodi' => 'D3 Teknik Mesin',
                'kode_prodi' => 'D3-TM',
                'kode_jurusan' => 'TM'
            ],
            [
                'nama_prodi' => 'D3 Teknologi Pemeliharaan Pesawat Udara',
                'kode_prodi' => 'D3-TPPU',
                'kode_jurusan' => 'TM'
            ],
            // TE
            [
                'nama_prodi' => 'S2 Teknik Elektro',
                'kode_prodi' => 'S2-TE',
                'kode_jurusan' => 'TE'
            ],
            [
                'nama_prodi' => 'D4 Teknik Elektronika',
                'kode_prodi' => 'D4-TE',
                'kode_jurusan' => 'TE'
            ],
            [
                'nama_prodi' => 'D4 Sistem Kelistrikan',
                'kode_prodi' => 'D4-SK',
                'kode_jurusan' => 'TE'
            ],
            [
                'nama_prodi' => 'D4 Jaringan Telekomunikasi Digital',
                'kode_prodi' => 'D4-JTD',
                'kode_jurusan' => 'TE'
            ],
            [
                'nama_prodi' => 'D3 Teknik Elektronika',
                'kode_prodi' => 'D3-TE',
                'kode_jurusan' => 'TE'
            ],
            [
                'nama_prodi' => 'D3 Teknik Telekomunikasi',
                'kode_prodi' => 'D3-TT',
                'kode_jurusan' => 'TE'
            ],
            [
                'nama_prodi' => 'D3 Teknik Listrik',
                'kode_prodi' => 'D3-TL',
                'kode_jurusan' => 'TE'
            ],
            // TS
            [
                'nama_prodi' => 'D4 Manajemen Rekayasa Konstruksi',
                'kode_prodi' => 'D4-MRK',
                'kode_jurusan' => 'TS'
            ],
            [
                'nama_prodi' => 'D4 Teknologi Rekayasa Konstruksi Jalan dan Jembatan',
                'kode_prodi' => 'D4-TRKJJ',
                'kode_jurusan' => 'TS'
            ],
            [
                'nama_prodi' => 'D3 Teknik Sipil',
                'kode_prodi' => 'D3-TS',
                'kode_jurusan' => 'TS'
            ],
            [
                'nama_prodi' => 'D3 Teknologi Konstruksi Jalan, Jembatan, dan Bangunan Air',
                'kode_prodi' => 'D3-TKJJBA',
                'kode_jurusan' => 'TS'
            ],
            [
                'nama_prodi' => 'D3 Teknologi Pertambangan',
                'kode_prodi' => 'D3-TP',
                'kode_jurusan' => 'TS'
            ],
            // TK
            [
                'nama_prodi' => 'D4 Teknik Kimia',
                'kode_prodi' => 'D4-TK',
                'kode_jurusan' => 'TK'
            ],
            [
                'nama_prodi' => 'D3 Teknik Kimia',
                'kode_prodi' => 'D3-TK',
                'kode_jurusan' => 'TK'
            ],
            // AN
            [
                'nama_prodi' => 'D4 Manajemen Pemasaran',
                'kode_prodi' => 'D4-MP',
                'kode_jurusan' => 'AN'
            ],
            [
                'nama_prodi' => 'D4 Bahasa Inggris untuk Komunikasi Bisnis dan Profesional',
                'kode_prodi' => 'D4-BIKBP',
                'kode_jurusan' => 'AN'
            ],
            [
                'nama_prodi' => 'D4 Pengelolaan Arsip dan Rekaman Informasi',
                'kode_prodi' => 'D4-PARI',
                'kode_jurusan' => 'AN'
            ],
            [
                'nama_prodi' => 'D4 Usaha Perjalanan Wisata',
                'kode_prodi' => 'D4-UPW',
                'kode_jurusan' => 'AN'
            ],
            [
                'nama_prodi' => 'D4 Bahasa Inggris Untuk Industri Pariwisata',
                'kode_prodi' => 'D4-BIIP',
                'kode_jurusan' => 'AN'
            ],
            [
                'nama_prodi' => 'D3 Administrasi Bisnis',
                'kode_prodi' => 'D3-AB',
                'kode_jurusan' => 'AN'
            ],
            // AK
            [
                'nama_prodi' => 'S2 Sistem Informasi Akuntansi',
                'kode_prodi' => 'S2-SIA',
                'kode_jurusan' => 'AK'
            ],
            [
                'nama_prodi' => 'D4 Akuntansi Manajemen',
                'kode_prodi' => 'D4-AM',
                'kode_jurusan' => 'AK'
            ],
            [
                'nama_prodi' => 'D4 Keuangan',
                'kode_prodi' => 'D4-Keu',
                'kode_jurusan' => 'AK'
            ],
            [
                'nama_prodi' => 'D3 Akuntansi',
                'kode_prodi' => 'D3-Ak',
                'kode_jurusan' => 'AK'
            ],
        ];

        foreach ($dataProdi as $prodi) {
            $jurusan = Jurusan::where('kode_jurusan', $prodi['kode_jurusan'])->first();

            if ($jurusan) {
                Prodi::create([
                    'nama_prodi' => $prodi['nama_prodi'],
                    'kode_prodi' => $prodi['kode_prodi'],
                    'jurusan_id' => $jurusan->id,
                ]);
            }
        }
    }
}
