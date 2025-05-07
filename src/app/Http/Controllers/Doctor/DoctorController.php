<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\GetUserPagedRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use App\Models\UserType;
use App\Modules\Exceptions\ValidationException;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\UserType\UserTypeRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    private UserRepositoryInterface $userRepository;
    private UserTypeRepositoryInterface $userTypeRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        UserTypeRepositoryInterface $userTypeRepository
    )
    {
        $this->userRepository = $userRepository;
        $this->userTypeRepository = $userTypeRepository;
    }

    /**
     *
     * @param GetUserPagedRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function index(): JsonResponse
    {
        $userType = $this->validateUserType(UserType::DOCTOR);

        return $this->apiResponsePages(UserResource::collection($this->userRepository->getByType($userType)));
    }

    private function validateUserType(int $id): ?UserType
    {
        $userType = $this->userTypeRepository->getById($id);

        if (!$userType) {
            throw new ValidationException("The user type does not exist.");
        }

        return $userType;
    }
}
