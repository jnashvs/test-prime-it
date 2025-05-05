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
        User::create([
            'name' => 'Receptionist',
            'email' => 'receptionist@patusco.com',
            'password' => Hash::make('password'),
            'user_type_id' => UserType::RECEPTIONIST,
        ]);

        User::create([
            'name' => 'Dr. Patusco',
            'email' => 'doctor@patusco.com',
            'password' => Hash::make('password'),
            'user_type_id' => UserType::DOCTOR,
        ]);

        User::create([
            'name' => 'John Doe',
            'email' => 'user@patusco.com',
            'password' => Hash::make('password'),
            'user_type_id' => UserType::USER,
        ]);
    }
}