<?php

namespace App\Repositories\User;

use App\Http\Requests\User\GetUserPagedRequest;
use App\Models\Factories\User\UserFactory;
use App\Models\User;
use App\Models\UserType;
use App\Modules\Exceptions\FatalModuleException;
use App\Modules\Exceptions\FatalRepositoryException;
use Illuminate\Support\Facades\Auth;

class UserRepository implements UserRepositoryInterface
{
    private UserFactory $userFactory;

    /**
     * @param UserFactory $userFactory
     */
    public function __construct(
        UserFactory $userFactory,
    ) {
        $this->userFactory = $userFactory;
    }

    public function getById(int $id): ?User
    {
        return $this->userFactory->getById($id);
    }

    /**
     * @param string $email
     * @return ?User
     */
    public function getByEmail(string $email): ?User
    {
        return $this->userFactory->getByEmail($email);
    }

    public function exists(string $email): bool
    {
        return User::where('email', $email)->exists();
    }

    public function get(?GetUserPagedRequest $request = null): mixed
    {
        if (!$request) {
            $request = new GetUserPagedRequest();
        }

        $searchKeyword = $request->input('search', null);
        $pageIndex = $request->input('pageIndex', 0);
        $pageSize = $request->input('pageSize', 20);
        $sortBy = $request->input('sortBy', 'id');
        $sortDesc = $request->boolean('sortDesc');
        $columns = $request->input('columns', ['email']);

        $result = $this->userFactory->get(
            $searchKeyword,
            $columns,
            $pageIndex - 1,
            $pageSize,
            $sortBy,
            $sortDesc
        );

        $result['rows'] = !empty($result['rows']) ? collect($result['rows'])->where('id', '<>', 1) : [];

        return $result;
    }


    /**
     * @param UserType $userType
     * @param string $name
     * @param string $email
     * @param ?string $password
     * @return User
     * @throws FatalRepositoryException
     */
    public function create(
        UserType $userType,
        string $name,
        string $email,
        ?string $password,
    ): User {
        return $this->userFactory->create($userType, $name, $email, $password);
    }

    /**
     * @param User $user
     * @param UserType $userType
     * @param string $name
     * @param string $email
     * @param ?string $password
     * @return User
     * @throws FatalRepositoryException
     */
    public function update(
        User $user,
        UserType $userType,
        string $name,
        string $email,
        ?string $password
    ): User {

        return $this->userFactory->update($user, $userType, $name, $email, $password);
    }

    /**
     * @param User $user
     * @return bool
     * @throws FatalModuleException
     */
    public function delete(User $user): bool
    {
        return $this->userFactory->delete($user);
    }

    public function getCurrentUser(): ?User
    {
        /** User $user */
        $user = Auth::user();

        if (!$user) {
            throw new FatalModuleException("User is not authenticated");
        }

        return $this->userFactory->getById($user->getId());
    }
}
