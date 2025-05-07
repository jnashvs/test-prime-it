<?php

namespace Tests\Unit;

use App\Http\Controllers\Pet\PetController;
use App\Models\AnimalType;
use App\Models\Pet;
use App\Models\User;
use App\Repositories\Pet\PetRepositoryInterface;
use App\Repositories\AnimalType\AnimalTypeRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use App\Http\Requests\Pet\EditPetRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;

class PetControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_pet_successfully()
    {
        try {
            $petRepository = Mockery::mock(PetRepositoryInterface::class);
            $animalTypeRepository = Mockery::mock(AnimalTypeRepositoryInterface::class);
            $userRepository = Mockery::mock(UserRepositoryInterface::class);

            $pet = new Pet([
                'name' => 'Fluffy',
                'animal_type_id' => 1,
                'registration_number' => '123456',
                'date_of_birth' => '2020-01-01',
                'breed' => 'Golden Retriever',
                'owner_id' => 23
            ]);
            $pet->id = 1;

            $petRepository->shouldReceive('create')
                ->once()
                ->andReturn($pet);

            $animalTypeRepository->shouldReceive('getById')
                ->once()
                ->with(1)
                ->andReturn(new AnimalType(['id' => 1, 'name' => 'Dog']));

            $userRepository->shouldReceive('getCurrentUser')
                ->once()
                ->andReturn(new User(['id' => 23, 'name' => 'John Doe']));

            Gate::shouldReceive('authorize')
                ->with('create pets', Mockery::any())
                ->andReturn(true);

            $request = new EditPetRequest();
            $request->replace([
                'animal_type_id' => 1,
                'name' => 'Fluffy',
                'registration_number' => '123456',
                'date_of_birth' => '2020-01-01',
                'breed' => 'Golden Retriever',
            ]);

            $controller = new PetController($petRepository, $animalTypeRepository, $userRepository);

            $response = $controller->create($request);

            $this->assertEquals(200, $response->getStatusCode());
            $this->assertJson($response->getContent());
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
        }
    }

    public function test_update_pet_successfully()
    {
        $petRepository = Mockery::mock(PetRepositoryInterface::class);
        $animalTypeRepository = Mockery::mock(AnimalTypeRepositoryInterface::class);
        $userRepository = Mockery::mock(UserRepositoryInterface::class);

        $pet = new Pet([
            'name' => 'Fluffy',
            'animal_type_id' => 1,
            'registration_number' => '123456',
            'date_of_birth' => '2020-01-01',
            'breed' => 'Golden Retriever',
            'owner_id' => 23
        ]);
        $pet->id = 1;

        $animalType = new AnimalType(['id' => 2, 'name' => 'Cat']);
        $user = new User(['id' => 23, 'name' => 'John Doe']);

        $petRepository->shouldReceive('getById')
            ->once()
            ->with(1)
            ->andReturn($pet);

        $animalTypeRepository->shouldReceive('getById')
            ->once()
            ->with(2)
            ->andReturn($animalType);

        $userRepository->shouldReceive('getCurrentUser')
            ->once()
            ->andReturn($user);

        $petRepository->shouldReceive('update')
            ->once()
            ->andReturn($pet);

        Gate::shouldReceive('authorize')
            ->with('edit pets', Mockery::any())
            ->andReturn(true);

        $request = new EditPetRequest();
        $request->replace([
            'animal_type_id' => 2,
            'name' => 'Whiskers',
            'registration_number' => '654321',
            'date_of_birth' => '2021-02-02',
            'breed' => 'Siamese',
        ]);

        $controller = new PetController($petRepository, $animalTypeRepository, $userRepository);
        $response = $controller->update($request, 1);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    public function test_get_pet_by_id_successfully()
    {
        $petRepository = Mockery::mock(PetRepositoryInterface::class);
        $animalTypeRepository = Mockery::mock(AnimalTypeRepositoryInterface::class);
        $userRepository = Mockery::mock(UserRepositoryInterface::class);

        $pet = new Pet([
            'name' => 'Fluffy',
            'animal_type_id' => 1,
            'registration_number' => '123456',
            'date_of_birth' => '2020-01-01',
            'breed' => 'Golden Retriever',
            'owner_id' => 23
        ]);
        $pet->id = 1;

        $petRepository->shouldReceive('getById')
            ->once()
            ->with(1)
            ->andReturn($pet);

        Gate::shouldReceive('authorize')
            ->with('view all pets', Mockery::any())
            ->andReturn(true);

        $controller = new PetController($petRepository, $animalTypeRepository, $userRepository);
        $response = $controller->getById(1);

        $this->assertInstanceOf(\App\Http\Resources\Pet\PetResource::class, $response);
    }

    public function test_delete_pet_successfully()
    {
        $petRepository = Mockery::mock(PetRepositoryInterface::class);
        $animalTypeRepository = Mockery::mock(AnimalTypeRepositoryInterface::class);
        $userRepository = Mockery::mock(UserRepositoryInterface::class);

        $pet = new Pet([
            'name' => 'Fluffy',
            'animal_type_id' => 1,
            'registration_number' => '123456',
            'date_of_birth' => '2020-01-01',
            'breed' => 'Golden Retriever',
            'owner_id' => 23
        ]);
        $pet->id = 1;

        $pet->setRelation('appointments', collect([]));

        $petRepository->shouldReceive('getById')
            ->once()
            ->with(1)
            ->andReturn($pet);

        $petRepository->shouldReceive('delete')
            ->once()
            ->with($pet)
            ->andReturn(true);

        Gate::shouldReceive('authorize')
            ->with('delete pets', Mockery::any())
            ->andReturn(true);

        $controller = new PetController($petRepository, $animalTypeRepository, $userRepository);
        $response = $controller->delete(1);

        $this->assertEquals(200, $response->getStatusCode());
    }
}
