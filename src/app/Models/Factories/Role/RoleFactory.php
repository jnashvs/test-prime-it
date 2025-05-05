<?php

namespace App\Models\Factories\Role;

use App\Models\Factories\AbstractFactory;
use Spatie\Permission\Models\Role;

class RoleFactory extends AbstractFactory
{
    public function __construct()
    {
        parent::__construct(Role::class);
    }

    public function getAllRoles()
    {
        return Role::with('permissions')->get();
    }

    // public function getById(int $id)
    // {
    //     return Role::with('permissions')->find($id);
    // }
}
