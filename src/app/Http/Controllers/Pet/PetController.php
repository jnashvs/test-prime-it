<?php

namespace App\Http\Controllers\Pet;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pet\EditPetRequest;
use App\Http\Requests\Pet\GetPetPagedRequest;
use App\Http\Resources\Pet\PetResource;
use App\Models\AnimalType;
use App\Models\Pet;
use App\Models\User;
use App\Modules\Exceptions\FatalModuleException;
use App\Modules\Exceptions\ValidationException;
use App\Repositories\AnimalType\AnimalTypeRepositoryInterface;
use App\Repositories\Pet\PetRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class PetController extends Controller
{
    private PetRepositoryInterface $petRepository;
    private AnimalTypeRepositoryInterface $animalTypeRepository;
    private UserRepositoryInterface $userRepository;

    public function __construct(
        PetRepositoryInterface $petRepository,
        AnimalTypeRepositoryInterface $animalTypeRepository,
        UserRepositoryInterface $userRepository
    )
    {
        $this->petRepository = $petRepository;
        $this->animalTypeRepository = $animalTypeRepository;
        $this->userRepository = $userRepository;
    }

    public function index(GetPetPagedRequest $request)
    {
        $values = $this->petRepository->get($request);

        return $this->apiResponsePages(PetResource::collection($values['rows']), $values['count']);
    }

    public function getById(int $id)
    {
        return new PetResource($this->petRepository->getById($id));
    }

    /**
     * @throws FatalModuleException
     * @throws ValidationException
     */
    public function create(EditPetRequest $request)
    {
        $animalType = $this->validateAnimalType($request->input('animal_type_id'));
        $user = $this->validateUser();

        $pet = $this->petRepository->create(
            $animalType,
            $user,
            $request->input('name'),
            $request->input('registration_number'),
            $request->input('date_of_birth'),
            $request->input('breed'),

        );

        return $this->apiResponse(new PetResource($pet));
    }

    /**
     * @throws FatalModuleException
     * @throws ValidationException
     */
    public function update(EditPetRequest $request, int $id)
    {
        $pet = $this->validatePet($id);
        $animalType = $this->validateAnimalType($request->input('animal_type_id'));

        $user = $this->validateUser();

        $pet = $this->petRepository->update(
            $pet,
            $animalType,
            $user,
            $request->input('name'),
            $request->input('registration_number'),
            $request->input('date_of_birth'),
            $request->input('breed'),
        );

        return $this->apiResponse(new PetResource($pet));
    }

    /**
     * @throws ValidationException
     */
    public function delete(int $id)
    {
        $pet = $this->validatePet($id);
        return $this->apiResponse($this->petRepository->delete($pet));
    }

    private function validateAnimalType(int $userTypeId): ?AnimalType
    {
        $animalType = $this->animalTypeRepository->getById($userTypeId);

        if (!$animalType) {
            throw new ValidationException("The animal type does not exist.");
        }

        return $animalType;
    }

    private function validatePet(int $id): ?Pet
    {
        $pet = $this->petRepository->getById($id);

        if (!$pet) {
            throw new ValidationException("The pet does not exist.");
        }

        return $pet;
    }

    /**
     * @throws FatalModuleException
     */
    private function validateUser(): ?User
    {
        return $this->userRepository->getCurrentUser();
    }
}
