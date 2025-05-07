<?php

namespace Tests\Feature;

use App\Models\AnimalType;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PetControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    public function test_user_can_create_pet()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('create pets');

        $animalType = AnimalType::factory()->create();

        $payload = [
            'animal_type_id' => $animalType->id,
            'name' => 'Fluffy',
            'registration_number' => '123456',
            'date_of_birth' => '2020-01-01',
            'breed' => 'Golden Retriever',
        ];

        $response = $this->actingAs($user)->postJson('/api/v1/pets', $payload);

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'Fluffy']);
        $this->assertDatabaseHas('pets', ['name' => 'Fluffy']);
    }

    public function test_user_can_view_pet()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('view all pets');

        $animalType = AnimalType::factory()->create();
        $pet = Pet::factory()->create([
            'animal_type_id' => $animalType->id,
            'owner_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->getJson("/api/v1/pets/{$pet->id}");

        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $pet->id]);
    }

    public function test_user_can_update_pet()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('edit pets');

        $animalType = AnimalType::factory()->create();
        $pet = Pet::factory()->create([
            'animal_type_id' => $animalType->id,
            'owner_id' => $user->id,
        ]);

        $updatePayload = [
            'animal_type_id' => $animalType->id,
            'name' => 'Updated Name',
            'registration_number' => '654321',
            'date_of_birth' => '2021-01-01',
            'breed' => 'Labrador',
        ];

        $response = $this->actingAs($user)->putJson("/api/v1/pets/{$pet->id}", $updatePayload);

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'Updated Name']);
        $this->assertDatabaseHas('pets', ['name' => 'Updated Name']);
    }

    public function test_user_can_delete_pet()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('delete pets');

        $animalType = AnimalType::factory()->create();
        $pet = Pet::factory()->create([
            'animal_type_id' => $animalType->id,
            'owner_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->deleteJson("/api/v1/pets/{$pet->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('pets', ['id' => $pet->id]);
    }

    public function test_user_can_list_pets()
    {
        $user = User::factory()->create();
        $user->givePermissionTo(['view all pets', 'view all appointments']);

        $animalType = AnimalType::factory()->create();
        Pet::factory()->count(3)->create([
            'animal_type_id' => $animalType->id,
            'owner_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->getJson('/api/v1/pets');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }
}
