<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AnimalType;

class AnimalTypeSeeder extends Seeder
{
    public function run()
    {
        $types = [
            ['name' => 'cão'],
            ['name' => 'gato'],
            ['name' => 'coelho'],
            ['name' => 'pássaro'],
            ['name' => 'hamster'],
            ['name' => 'tartaruga'],
        ];

        foreach ($types as $type) {
            AnimalType::updateOrCreate(
                ['name' => $type['name']],
                []
            );
        }
    }
}
