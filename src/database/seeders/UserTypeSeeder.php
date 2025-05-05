<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserType;

class UserTypeSeeder extends Seeder
{
    public function run()
    {
        $types = [
            ['id' => UserType::RECEPTIONIST, 'name' => 'receptionist'],
            ['id' => UserType::DOCTOR, 'name' => 'doctor'],
            ['id' => UserType::USER, 'name' => 'user'],
        ];

        foreach ($types as $type) {
            UserType::updateOrCreate(
                ['id' => $type['id']],
                ['name' => $type['name']]
            );
        }
    }
}
