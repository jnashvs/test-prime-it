<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\EditUserRequest;
use App\Models\UserType;
use App\Modules\Exceptions\ValidationException;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\UserType\UserTypeRepositoryInterface;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{

    private UserTypeRepositoryInterface $userTypeRepository;
    private UserRepositoryInterface $userRepository;

    public function __construct(
        UserTypeRepositoryInterface $userTypeRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->userTypeRepository = $userTypeRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Show the registration page.
     */
    public function create(): Response
    {
        return Inertia::render('auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     * @throws ValidationException
     */
    public function store(EditUserRequest $request): RedirectResponse
    {

        $userType = $this->validateUserTypeExist(UserType::USER);

        $user = $this->userRepository->create(
            $userType,
            $request->input('name'),
            $request->input('email'),
            $request->input('password'),
        );

        event(new Registered($user));

        Auth::login($user);

        return to_route('dashboard');
    }

    /**
     * @param int $userTypeId
     * @return ?UserType
     * @throws ValidationException
     */
    private function validateUserTypeExist(int $userTypeId): ?UserType
    {
        $userType = $this->userTypeRepository->getById($userTypeId);

        if (!$userType) {
            throw new ValidationException("The user type does not exist.");
        }

        return $userType;
    }
}
