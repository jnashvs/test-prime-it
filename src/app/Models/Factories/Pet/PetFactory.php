<?php

namespace App\Models\Factories\Pet;

use App\Models\AnimalType;
use App\Models\Factories\AbstractFactory;
use App\Models\Pet;
use App\Models\User;
use App\Modules\Exceptions\FatalRepositoryException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class PetFactory extends AbstractFactory
{
    public function __construct()
    {
        parent::__construct(Pet::class);
    }

    /**
     * @param AnimalType $animalType
     * @param User $owner
     * @param string $name
     * @param string $registrationNumber
     * @param string $dateOfBirth
     * @param ?string $breed
     * @return Pet
     * @throws FatalRepositoryException
     */
    public function create(
        AnimalType $animalType,
        User $owner,
        string $name,
        string $registrationNumber,
        string $dateOfBirth,
        ?string $breed,
    ): Pet
    {
        $pet = new Pet();
        return $this->update(
            $pet,
            $animalType,
            $owner,
            $name,
            $registrationNumber,
            $dateOfBirth,
            $breed,
        );
    }


    /**
     * @param Pet $pet
     * @param AnimalType $animalType
     * @param User $owner
     * @param string $name
     * @param string $registrationNumber
     * @param string $dateOfBirth
     * @param string|null $breed
     * @return Pet
     * @throws FatalRepositoryException
     */
    public function update(
        Pet $pet,
        AnimalType $animalType,
        User $owner,
        string $name,
        string $registrationNumber,
        string $dateOfBirth,
        ?string $breed,
    ): Pet
    {
        $pet->setAnimalType($animalType);
        $pet->setOwner($owner);
        $pet->setName($name);
        $pet->setRegistrationNumber($registrationNumber);
        $pet->setDateOfBirth($dateOfBirth);
        $pet->setBreed($breed);

        if (!$pet->save()) {
            throw new FatalRepositoryException('Failed to create/update a pet.');
        }

        return $pet;
    }

    public function getFilter(
        ?string $searchKeyword,
        array $columns,
        int $pageIndex,
        int $pageSize,
        string $sortBy,
        bool $sortDesc,
        ?int $typeId,
        ?string $dateFrom,
        ?string $dateTo,
        ?int $ownerId = null // Added new parameter for ownerId
    ): mixed {

        try {
            $query = Pet::query();

            // Add owner_id filter if provided
            if ($ownerId !== null) {
                $query->where('owner_id', $ownerId);
            }

            if ($searchKeyword) {
                $query->where(function ($q) use ($searchKeyword, $columns) {
                    foreach ($columns as $column) {
                        $q->orWhere($column, 'LIKE', '%' . $searchKeyword . '%');
                    }
                });
            }

            if ($typeId) {
                $query->where('animal_type_id', $typeId);
            }

            if ($dateFrom) {
                $query->where('date_of_birth', '>=', $dateFrom);
            }

            if ($dateTo) {
                $query->where('date_of_birth', '<=', $dateTo);
            }

            $sortOrder = $sortDesc ? 'DESC' : 'ASC';
            $query->orderBy($sortBy, $sortOrder);

            $count = $query->count();

            $rows = $query->skip($pageIndex * $pageSize)->take($pageSize)->get();

            return [
                'count' => $count,
                'rows' => $rows,
            ];
        } catch (\Throwable $th) {
            Log::error('Error in get function: ' . $th->getMessage());
            return [
                'count' => 0,
                'rows' => new Collection(),
            ];
        }
    }
}
