<?php

namespace App\Models\Factories\User;

use App\Http\Requests\General\GetGeneralPagedRequest;
use App\Http\Requests\User\GetUserPagedRequest;
use App\Models\User;
use App\Models\Factories\AbstractFactory;
use App\Models\Status;
use App\Models\UserType;
use App\Modules\Exceptions\FatalRepositoryException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

/**
 *
 */
class UserFactory extends AbstractFactory
{

    /**
     *
     */
    public function __construct()
    {
        parent::__construct(User::class);
    }

    /**
     * @param string $email
     * @return bool
     */
    public function exists(string $email): bool
    {
        return User::where('email', $email)->exists();
    }

    /**
     * @param string $email
     * @return ?User
     */
    public function getByEmail(string $email): ?User
    {
        return User::query()->where('email', $email)->get()->first();
    }

    /**
     * Get backoffice users with search, pagination and sorting
     *
     * @param string|null $search Search term for email, first name or last name
     * @param int $pageIndex Page number (default: 0)
     * @param int $pageSize Number of items per page (default: 10)
     * @param string $sortBy Field to sort by (default: 'created_at')
     * @param bool $sortDesc Sort direction (default: true)
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getQuery(
        ?string $search = null,
        int $pageIndex = 0,
        int $pageSize = 10,
        string $sortBy = 'created_at',
        bool $sortDesc = true,
    ) {
        $query = User::query();

        // Apply search if provided
        if ($search) {
            // Split the search string into individual words
            $searchTerms = array_filter(explode(' ', mb_strtolower($search)));

            $query->where(function ($q) use ($search, $searchTerms) {
                // Search email for the entire term (works well with ilike)
                $q->where('email', 'ilike', '%' . implode('%', $searchTerms) . '%')
                    // Group first name and last name conditions
                    ->orWhere(function ($q) use ($searchTerms) {
                        foreach ($searchTerms as $term) {
                            $q->orWhereRaw(
                                'LOWER(unaccent(first_name)) LIKE LOWER(unaccent(?))',
                                ['%' . $term . '%']
                            );
                            $q->orWhereRaw(
                                'LOWER(unaccent(last_name)) LIKE LOWER(unaccent(?))',
                                ['%' . $term . '%']
                            );
                        }
                    });
            });
        }

        // Apply sorting
        $sortOrder = $sortDesc ? 'DESC' : 'ASC';
        $query->orderBy($sortBy, $sortOrder);

        // Get the number of results
        $count = $query->count();

        // Fetch paginated data
        $rows = $query->skip($pageIndex * $pageSize)->take($pageSize)->get();

        return [
            'count' => $count,
            'rows' => $rows,
        ];
    }

    /**
     * @param UserType $userType
     * @param string $name
     * @param string $email
     * @param ?string $password
     * @return User
     * @throws FatalRepositoryException
     */
    public function create(
        UserType $userType,
        string $name,
        string $email,
        ?string $password
    ): User {
        $user = new User();

        return $this->update($user, $userType, $name, $email, $password);
    }

    /**
     * @param User $user
     * @param UserType $userType
     * @param string $name
     * @param string $email
     * @param ?string $password
     * @return User
     * @throws FatalRepositoryException
     */
    public function update(
        User $user,
        UserType $userType,
        string $name,
        string $email,
        ?string $password
    ): User {

        $user->setUserType($userType);
        $user->setName($name);
        $user->setEmail($email);

        if ($password) {
            $user->setPassword($this->setHashPassword($password));
        }

        if (!$user->save()) {
            throw new FatalRepositoryException('Failed to create/update a user.');
        }

        return $user;
    }

    private function setHashPassword($password): string {
        return Hash::make($password);
    }
}
