<?php

namespace Database\Seeders;

use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use App\Models\Organization;
use Illuminate\Database\Seeder;
use App\Models\OrganizationMember;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OrganizationMemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a Faker instance
        $faker = Faker::create();

        // Retrieve all organizations
        $organizations = Organization::all();
        foreach ($organizations as $organization) {

            for ($i = 0; $i < 20; $i++) {
                $user = User::create([
                    'name' => $faker->name,
                    'username' => 'member_' . strtolower(Str::random(5)) . rand(100, 999),
                    'email' => $faker->unique()->safeEmail,
                    'password' => Hash::make('password'),
                    'is_active' => $i == 4 ? false : true,
                ]);
                OrganizationMember::create([
                    'organization_id' => $organization->id,
                    'user_id' => $user->id,
                    'nim' => rand(100000, 999999),
                    'name' => $faker->name,
                    'level' => $faker->randomElement(['SC', 'OC']),
                    'position' => $faker->jobTitle,
                    'is_leader' => $i == 0 ? true : false,
                    'is_active' => $i == 4 ? false : true,
                ]);
            }
        }
    }
}
