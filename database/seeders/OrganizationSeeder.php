<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Jurusan;
use App\Models\Organization;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data Organization
        $organizations = [[
            "LT" =>  [
                [
                    "name" => 'Dewan Perwakilan Mahasiswa',
                    "username_email" => 'dpm_polinema',
                    "logo" => 'DPM',
                    "shorten_name" => 'DPM',
                ],
                [
                    "name" => 'Badan Eksekutif Mahasiswa',
                    "username_email" => 'bem_polinema',
                    "logo" => 'bem',
                    "shorten_name" => 'BEM',
                ],
            ],
            "HMJ" => [
                [
                    "name" => 'Himpunan Mahasiswa Teknologi Informasi',
                    "username_email" => 'hmti_polinema',
                    "logo" => 'hmti',
                    "shorten_name" => 'HMTI',
                    "jurusan" => 'TI',
                ],
                [
                    "name" => 'Himpunan Mahasiswa Akuntansi',
                    "username_email" => 'hma_polinema',
                    "logo" => 'hma',
                    "shorten_name" => 'HMA',
                    "jurusan" => 'AK',
                ],
                [
                    "name" => 'Himpunan Mahasiswa Teknik Mesin',
                    "username_email" => 'hmm_polinema',
                    "logo" => 'hmm',
                    "shorten_name" => 'HMM',
                    "jurusan" => 'TM',
                ],
                [
                    "name" => 'Himpunan Mahasiswa Elektro',
                    "username_email" => 'hme_polinema',
                    "logo" => 'hme',
                    "shorten_name" => 'HME',
                    "jurusan" => 'TE',
                ],
                [
                    "name" => 'Himpunan Mahasiswa Teknik Kimia',
                    "username_email" => 'hmtk_polinema',
                    "logo" => 'hmtk',
                    "shorten_name" => 'HMTK',
                    "jurusan" => 'TK',
                ],
                [
                    "name" => 'Himpunan Mahasiswa Administrasi Niaga',
                    "username_email" => 'himania_polinema',
                    "logo" => 'himania',
                    "shorten_name" => 'HIMANIA',
                    "jurusan" => 'AN',
                ],
                [
                    "name" => 'Himpunan Mahasiswa Teknik Sipil',
                    "username_email" => 'hms_polinema',
                    "logo" => 'hms',
                    "shorten_name" => 'HMS',
                    "jurusan" => 'TS',
                ],
            ],
            "UKM" =>  [
                [
                    "name" => 'UKM Penalaran dan Pendidikan',
                    "username_email" => 'pp_polinema',
                    "logo" => 'pp',
                    "shorten_name" => 'UKM PP',
                ],
                [
                    "name" => 'UKM Bhakti Karya Mahasiswa',
                    "username_email" => 'bkm_polinema',
                    "logo" => 'BKM',
                    "shorten_name" => 'UKM BKM',
                ],
                [
                    "name" => 'UKM Resimen Mahasiswa',
                    "username_email" => 'menwa_polinema',
                    "logo" => 'menwa',
                    "shorten_name" => 'UKM Menwa',
                ],
            ],

        ]];


        foreach ($organizations as  $organization) {
            foreach ($organization as $type => $org) {
                foreach ($org as $content) {
                    if ($type === 'HMJ' || $type === 'Jurusan') {
                        $jurusan_id = Jurusan::where('kode_jurusan', $content["jurusan"])->value('id');
                        $user = User::create(
                            [
                                'name' => $content["name"],
                                'username' => $content["username_email"],
                                'email' => $content["username_email"] . '.evoting@arobidsh.id',
                                'password' => Hash::make('password'),
                                'jurusan_id' => $jurusan_id,
                            ]
                        );
                    } else {
                        $user = User::create(
                            [
                                'name' => $content["name"],
                                'username' => $content["username_email"],
                                'email' => $content["username_email"] . '.evoting@arobidsh.id',
                                'password' => Hash::make('password')
                            ]
                        );
                    }
                    Organization::create([
                        'shorten_name' => $content["shorten_name"],
                        'vision' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Vitae sunt, eligendi accusantium quidem.',
                        'mision' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur doloribus accusamus ducimus, architecto cum eveniet ea ipsa? Sint, eum dolores?|Lorem ipsum dolor sit amet consectetur adipisicing elit. Consequatur doloribus accusamus ducimus|Lorem ipsum ducimus, architecto cum eveniet ea ipsa? Sint, eum dolores?|amet consectetur adipisicing elit. Consequatur doloribus accusamus ducimus, architecto cum eveniet ea ipsa? Sint, eum dolores?',
                        'description' => 'Repudiandae, eius impedit aliquid magni excepturi quam.',
                        'whatsapp_number' => '082344444444',
                        'user_id' => $user->id,
                        'organization_type' => $type,
                        'logo' => $type === 'Kampus' ? 'assets/images/logo_polinema.png' : 'assets/images/logo_organizers/' . $content["logo"] . '.png',
                    ]);
                    $user->assignRole('Organization');
                }
            }
        }
    }
}
