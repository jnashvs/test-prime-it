<?php

namespace App\Repositories\UserType;

use App\Models\UserType;
use Illuminate\Database\Eloquent\Collection;

interface UserTypeRepositoryInterface
{
    /**
     * @param int $id
     * @return ?UserType
     */
    public function getById(int $id): ?UserType;

    public function getAll(): ?Collection;
}
