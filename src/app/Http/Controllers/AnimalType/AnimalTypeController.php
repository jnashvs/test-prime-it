<?php

namespace App\Http\Controllers\AnimalType;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnimalType\AnimalTypeResource;
use App\Repositories\AnimalType\AnimalTypeRepositoryInterface;

class AnimalTypeController extends Controller
{
    private AnimalTypeRepositoryInterface $animalTypeRepository;

    public function __construct(AnimalTypeRepositoryInterface $animalTypeRepository)
    {
        $this->animalTypeRepository = $animalTypeRepository;
    }

    public function index()
    {
        return $this->apiResponse(AnimalTypeResource::collection($this->animalTypeRepository->getAll()));
    }

    public function getById(int $id)
    {
        return $this->apiResponse(new AnimalTypeResource($this->animalTypeRepository->getById($id)));
    }
}
