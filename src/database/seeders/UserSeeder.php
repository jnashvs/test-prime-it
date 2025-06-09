<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Define the common password for all seeded users
        $password = Hash::make('password');

        // Create 3 receptionists for test usage
        for ($i = 1; $i <= 3; $i++) {
            $receptionist = User::firstOrCreate(
                ['email' => "receptionist$i@patusco.com"],
                [
                    'name' => "Receptionist $i",
                    'password' => $password,
                    'user_type_id' => UserType::RECEPTIONIST,
                ]
            );
            $receptionist->assignRole('receptionist');
        }

        // Create 5 doctors for test usage
        for ($i = 1; $i <= 5; $i++) {
            $doctor = User::firstOrCreate(
                ['email' => "doctor$i@patusco.com"],
                [
                    'name' => "Dr. Patusco $i",
                    'password' => $password,
                    'user_type_id' => UserType::DOCTOR,
                ]
            );
            $doctor->assignRole('doctor');
        }

        // Create a single user
        $user = User::firstOrCreate(
            ['email' => 'user@patusco.com'],
            [
                'name' => 'John Doe',
                'password' => $password,
                'user_type_id' => UserType::USER,
            ]
        );
        $user->assignRole('user');
    }
}
