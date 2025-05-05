<?php

namespace App\Repositories\UserType;

use App\Models\Factories\UserType\UserTypeFactory;
use App\Models\UserType;
use Illuminate\Database\Eloquent\Collection;

class UserTypeRepository implements UserTypeRepositoryInterface
{
    private UserTypeFactory $userTypeFactory;
    public function __construct(UserTypeFactory $userTypeFactory) {
        $this->userTypeFactory = $userTypeFactory;
    }

    public function getById(int $id): ?UserType
    {
        return $this->userTypeFactory->getById($id);
    }

    public function getAll(): ?Collection
    {
        return $this->userTypeFactory->getAll();
    }

}
