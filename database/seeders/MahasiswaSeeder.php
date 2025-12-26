<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Prodi;
use App\Models\Mahasiswa;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $prodis = Prodi::all();

        foreach ($prodis as $prodi) {
            for ($i = 1; $i <= 8; $i++) {
                $user = User::create([
                    'name' => 'Mahasiswa ' . $i . ' - ' . $prodi->nama_prodi,
                    'username' => 'mahasiswa_' . $i . '_' . $prodi->kode_prodi,
                    'email' => 'mahasiswa' . $i . '_' . Str::slug($prodi->nama_prodi) . '@mail.com',
                    'password' => Hash::make('password'),
                    'is_active' => $i <= 5 ? true : false,
                    'jurusan_id' => $prodi->jurusan_id,
                ]);
                $user->assignRole('Voter');
                Mahasiswa::create([
                    'user_id' => $user->id,
                    'prodi_id' => $prodi->id,
                    'nim' => 'NIM' . rand(100000, 999999),
                    'tanggal_lahir' => now()->subYears(rand(18, 24))->subDays(rand(0, 365)),
                    'jenis_kelamin' => ['Laki-laki', 'Perempuan'][rand(0, 1)],
                    'status' => $i <= 5 ? 'Aktif' : ($i == 6 ? 'Cuti' : ($i == 7 ? 'Lulus' : 'Drop Out')),
                ]);
            }
        }
    }
}
