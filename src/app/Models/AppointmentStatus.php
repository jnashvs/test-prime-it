<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AppointmentStatus extends Model
{
    public $timestamps = true;

    public const REQUESTED = 1;
    public const PENDING_ASSIGNMENT = 2;
    public const ASSIGNED = 3;
    public const COMPLETED = 4;
    public const CANCELLED = 5;

    protected $fillable = [
        'name',
        'description',
    ];

    // relationships
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'status_id');
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }
}