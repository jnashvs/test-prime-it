<?php

namespace App\Repositories\AnimalType;

use App\Models\AnimalType;
use Illuminate\Database\Eloquent\Collection;

interface AnimalTypeRepositoryInterface
{
    /**
     * @param int $id
     * @return ?AnimalType
     */
    public function getById(int $id): ?AnimalType;

    /**
     * @return Collection
     */
    public function getAll(): Collection;
}