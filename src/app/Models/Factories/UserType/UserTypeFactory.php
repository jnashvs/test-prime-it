<?php

namespace App\Models\Factories\UserType;

use App\Models\Factories\AbstractFactory;
use App\Models\UserType;

class UserTypeFactory extends AbstractFactory
{
    public function __construct()
    {
        parent::__construct(UserType::class);
    }
}
