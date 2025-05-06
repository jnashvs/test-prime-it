<?php

namespace App\Repositories\Pet;

use App\Http\Requests\Pet\GetPetPagedRequest;
use App\Http\Requests\User\GetUserPagedRequest;
use App\Models\AnimalType;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface PetRepositoryInterface
{
    public function getById(int $id): ?Pet;

    public function getAll(): Collection;

    /**
     * @param ?GetPetPagedRequest $request
     * @return mixed
     */
    public function get(GetPetPagedRequest $request = null): mixed;

    /**
     * @param AnimalType $animalType
     * @param User $owner
     * @param string $name
     * @param string $registrationNumber
     * @param string $dateOfBirth
     * @param ?string $breed
     * @return Pet
     */
    public function create(
        AnimalType $animalType,
        User $owner,
        string $name,
        string $registrationNumber,
        string $dateOfBirth,
        ?string $breed,
    ): Pet;

    /**
     * @param Pet $pet
     * @param AnimalType $animalType
     * @param User $owner
     * @param string $name
     * @param string $registrationNumber
     * @param string $dateOfBirth
     * @param ?string $breed
     * @return Pet
     */
    public function update(
        Pet $pet,
        AnimalType $animalType,
        User $owner,
        string $name,
        string $registrationNumber,
        string $dateOfBirth,
        ?string $breed,
    ): Pet;

    public function delete(Pet $pet): bool;
}
