<?php

namespace Database\Factories;

use App\Models\Pet;
use App\Models\User;
use App\Models\AnimalType;
use Illuminate\Database\Eloquent\Factories\Factory;

class PetFactory extends Factory
{
    protected $model = Pet::class;

    public function definition()
    {
        return [
            'name' => $this->faker->firstName,
            'registration_number' => $this->faker->unique()->numerify('######'),
            'animal_type_id' => AnimalType::factory(),
            'date_of_birth' => $this->faker->date(),
            'owner_id' => User::factory(),
            'breed' => $this->faker->word,
        ];
    }
}