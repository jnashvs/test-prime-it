<?php

namespace App\Http\Resources\Pet;

use App\Http\Resources\AnimalType\AnimalTypeResource;
use App\Http\Resources\BaseResource;
use App\Http\Resources\User\UserResource;
use App\Models\Pet;

class PetResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param $item
     * @return array
     */
    public function process($item): array
    {
        /**
         * @var Pet $item
         */
        return [
            'id' => $item->getId(),
            'name' => $item->getName(),
            'registration_number' => $item->getRegistrationNumber(),
            'animal_type_id' => $item->getAnimalTypeId(),
            'animal_type' => new AnimalTypeResource($item->animalType),
            'date_of_birth' => $item->getDateOfBirth(),
            'age' => $item->getAge(),
            'owner_id' => $item->getOwnerId(),
            'owner' => new UserResource($item->owner)
        ];
    }
}
