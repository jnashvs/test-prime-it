<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Pet extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'registration_number',
        'animal_type_id',
        'date_of_birth',
        'owner_id',
        'breed',
    ];

    // Relationships
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function animalType(): BelongsTo
    {
        return $this->belongsTo(AnimalType::class, 'animal_type_id');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
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

    public function getRegistrationNumber(): ?string
    {
        return $this->registration_number;
    }

    public function setRegistrationNumber(?string $registrationNumber): void
    {
        $this->registration_number = $registrationNumber;
    }

    public function getAnimalTypeId(): ?int
    {
        return $this->animal_type_id;
    }

    public function setAnimalType(AnimalType $animalType): void
    {
        $this->animal_type_id = $animalType->getId();
    }

    public function getDateOfBirth(): ?string
    {
        return $this->date_of_birth;
    }

    public function setDateOfBirth(?string $dateOfBirth): void
    {
        $this->date_of_birth = $dateOfBirth;
    }

    public function getBreed(): ?string
    {
        return $this->breed;
    }

    public function setBreed(?string $breed): void
    {
        $this->breed = $breed;
    }

    public function getAge(): ?int
    {
        if ($this->getDateOfBirth()) {
            return Carbon::parse($this->getDateOfBirth())->age;
        }
        return null;
    }

    public function getOwnerId(): ?int
    {
        return $this->owner_id;
    }

    public function setOwner(?User $user): void
    {
        $this->owner_id = $user?->getId();
    }

    public function getCreatedAt(): ?string
    {
        return $this->created_at?->toDateTimeString();
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updated_at?->toDateTimeString();
    }
    }
