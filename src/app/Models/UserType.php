<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserType extends Model
{
    public $timestamps = true;

    public const RECEPTIONIST = 1;
    public const DOCTOR = 2;
    public const USER = 3;

    protected $fillable = [
        'name',
    ];

    // relationships
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'user_type_id');
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