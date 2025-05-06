<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create 3 receptionists for tet usage
        for ($i = 1; $i <= 3; $i++) {
            $receptionist = User::create([
                'name' => "Receptionist $i",
                'email' => "receptionist$i@patusco.com",
                'password' => Hash::make('password'),
                'user_type_id' => UserType::RECEPTIONIST,
            ]);
            $receptionist->assignRole('receptionist');
        }

        // Create 5 doctors for test usage
        for ($i = 1; $i <= 5; $i++) {
            $doctor = User::create([
                'name' => "Dr. Patusco $i",
                'email' => "doctor$i@patusco.com",
                'password' => Hash::make('password'),
                'user_type_id' => UserType::DOCTOR,
            ]);
            $doctor->assignRole('doctor');
        }

        // Create a user
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'user@patusco.com',
            'password' => Hash::make('password'),
            'user_type_id' => UserType::USER,
        ]);
        $user->assignRole('user');
    }
}
