<?php

namespace App\Models\Factories\AnimalType;

use App\Models\Factories\AbstractFactory;
use App\Models\AnimalType;

class AnimalTypeFactory extends AbstractFactory
{
    public function __construct()
    {
        parent::__construct(AnimalType::class);
    }
}