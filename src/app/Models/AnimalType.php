<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnimalType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    // relationships
    public function pets(): HasMany
    {
        return $this->hasMany(Pet::class, 'animal_type_id');
    }

    // Getters and Setters
    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }
}
