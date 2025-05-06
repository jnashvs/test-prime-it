<?php

namespace App\Repositories\Pet;

use App\Http\Requests\Pet\GetPetPagedRequest;
use App\Models\AnimalType;
use App\Models\Factories\Pet\PetFactory;
use App\Models\Pet;
use App\Models\User;
use App\Models\UserType;
use App\Modules\Exceptions\FatalRepositoryException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth; // Added for accessing authenticated user

class PetRepository implements PetRepositoryInterface
{
    private PetFactory $petFactory;

    public function __construct(PetFactory $petFactory)
    {
        $this->petFactory = $petFactory;
    }

    public function getById(int $id): ?Pet
    {
        return $this->petFactory->getById($id);
    }

    public function getAll(): Collection
    {
        return $this->petFactory->getAll();
    }

    public function get(?GetPetPagedRequest $request = null): mixed
    {
        if (!$request) {
            $request = new GetPetPagedRequest();
        }

        $searchKeyword = $request->input('search', null);
        $pageIndex = $request->input('pageIndex', 0);
        $pageSize = $request->input('pageSize', 20);
        $sortBy = $request->input('sortBy', 'id');
        $sortDesc = $request->boolean('sortDesc');
        $columns = $request->input('columns', ['name', 'registration_number', 'breed']);
        $typeId = $request->input('typeId');
        $dateFrom = $request->input('dateFrom');
        $dateTo = $request->input('dateTo');

        $ownerIdToFilter = null;
        /** @var User $authenticatedUser */
        $authenticatedUser = Auth::user();

        if ($authenticatedUser && $authenticatedUser->isUser()) {
            $ownerIdToFilter = $authenticatedUser->getId();
        }

        return $this->petFactory->getFilter(
            $searchKeyword,
            $columns,
            $pageIndex > 0 ? $pageIndex - 1 : 0,
            $pageSize,
            $sortBy,
            $sortDesc,
            $typeId,
            $dateFrom,
            $dateTo,
            $ownerIdToFilter
        );
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
        return $this->petFactory->create(
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
     * @param ?string $breed
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
        return $this->petFactory->update(
            $pet,
            $animalType,
            $owner,
            $name,
            $registrationNumber,
            $dateOfBirth,
            $breed,
        );
    }

    public function delete(Pet $pet): bool
    {
        return $this->petFactory->delete($pet);
    }
}
