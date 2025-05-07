<?php

namespace App\Repositories\User;

use App\Http\Requests\User\GetUserPagedRequest;
use App\Models\User;
use App\Models\UserType;

/**
 *
 */
interface UserRepositoryInterface
{
    /**
     * @param int $id
     * @return User|null
     */
    public function getById(int $id): ?User;

    /**
     * @param string $email
     * @return ?User
     */
    public function getByEmail(string $email): ?User;

    /**
     * @param UserType $userType
     * @return bool
     */
    public function getByType(UserType $userType): mixed;

    /**
     * @return mixed
     */
    public function get(GetUserPagedRequest $request = null): mixed;

    /**
     * @param UserType $userType
     * @param string $name
     * @param string $email
     * @param ?string $password
     * @return User
     */
    public function create(
        UserType $userType,
        string $name,
        string $email,
        ?string $password
    ): User;

    /**
     * @param User $user
     * @param UserType $userType
     * @param string $name
     * @param string $email
     * @param ?string $password
     * @return User
     */
    public function update(
        User $user,
        UserType $userType,
        string $name,
        string $email,
        ?string $password
    ): User;

    public function delete(User $user): bool;
}
