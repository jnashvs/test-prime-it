<?php

namespace App\Repositories\AnimalType;

use App\Models\Factories\AnimalType\AnimalTypeFactory;
use App\Models\AnimalType;
use Illuminate\Database\Eloquent\Collection;

class AnimalTypeRepository implements AnimalTypeRepositoryInterface
{
    private AnimalTypeFactory $animalTypeFactory;

    public function __construct(AnimalTypeFactory $animalTypeFactory)
    {
        $this->animalTypeFactory = $animalTypeFactory;
    }

    public function getById(int $id): ?AnimalType
    {
        return $this->animalTypeFactory->getById($id);
    }

    public function getAll(): Collection
    {
        return $this->animalTypeFactory->getAll();
    }
}