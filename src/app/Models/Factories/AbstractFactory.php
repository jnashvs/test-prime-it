<?php

namespace App\Models\Factories;

use App\Modules\Exceptions\FatalModuleException;
use App\Modules\Exceptions\FatalRepositoryException;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * Abstract repository to handle Model queries
 */
abstract class AbstractFactory implements FactoryInterface
{

    public const REPO_NAMESPACE = "App\\Repositories\\";

    /**
     * @var Model $model
     */
    public $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    /**
     * @inheritDoc
     */
    public function getById(int $id, array $arrRelations = [], ?string $dto = null, bool $isPublic = false): ?Model
    {
        if ($isPublic) {
            $objBuilder = $this->model::withAllData();
        } else {
            $objBuilder = $this->model::query();
        }
        $objBuilder->where('id', '=', $id);

        foreach ($arrRelations as $relation) {
            $objBuilder->with($relation);
        }

        return $objBuilder->first();
    }

    /**
     * @inheritDoc
     */
    public function getByIds(array $ids, array $arrRelations = [], string $dto = null, bool $isPublic = false): Collection
    {
        $objBuilder = $this->model::query()
            ->whereIn('id', $ids);

        foreach ($arrRelations as $relation) {
            $objBuilder->with($relation);
        }

        return $objBuilder
            ->orderByRaw("FIELD(id, " . implode(',', $ids) . ")")
            ->get();
    }

    /**
     * @inheritDoc
     */
    public function get(
        ?string $searchKeyword = null,
        array $searchByColumns = [],
        int $pageIndex = 0,
        int $pageSize = 25,
        string $sortBy = 'id',
        bool $sortDesc = true,
        Carbon|null $dateFrom = null,
        Carbon|null $dateTo = null,
        array $arrRelations = [],
        bool $isPublic = false
    ): mixed {
        try {
            $query = $isPublic ? $this->model::withAllData() : $this->model::query();

            foreach ($arrRelations as $relation) {
                $query->with($relation);
            }

            // Apply search if provided
            if ($searchKeyword) {
                $query->where(function ($q) use ($searchKeyword, $searchByColumns) {
                    foreach ($searchByColumns as $column) {
                        $q->orWhere($column, 'LIKE', "%$searchKeyword%");
                    }
                });
            }

            // Apply date filters
            $query->when($dateFrom, fn ($q) => $q->where('date', '>=', $dateFrom))
                ->when($dateTo, fn ($q) => $q->where('date', '<=', $dateTo));

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
        } catch (\Throwable $th) {
            // Log the error or handle it as needed
            Log::error('Error in get function: ' . $th->getMessage());
            return [
                'count' => 0,
                'rows' => new Collection(),
            ];
        }
    }

    /**
     * @inheritDoc
     */
    public function getAll(array $arrRelations = [], bool $isPublic = false): Collection
    {
        if ($isPublic) {
            $objBuilder = $this->model::withAllData();
        } else {
            $objBuilder = $this->model::query();
        }

        foreach ($arrRelations as $relation) {
            $objBuilder->with($relation);
        }

        return $objBuilder->get();
    }

    /**
     * @inheritDoc
     */
    public function getAllSelectedField($field=[]): Collection
    {
        $objBuilder = $this->model::query();
        if (!empty($field)) {
            $objBuilder->select($field);
        }
        return $objBuilder->get();
    }

    /**
     * @inheritDoc
     */
    public function getAllObjects(array $arrRelations = [], bool $isPublic = false): array
    {
        if ($isPublic) {
            $objBuilder = $this->model::withAllData();
        } else {
            $objBuilder = $this->model::query();
        }
        foreach ($arrRelations as $relation) {
            $objBuilder->with($relation);
        }

        return $objBuilder->get()->all();
    }

    public function getQuery()
    {
        return $this->model::query();
    }

    /**
     * @inheritDoc
     */
    public function updateByModel($model, array $arrData): Model
    {
        try {
            $result = $model->update($arrData);
            if (empty($result)) {
                throw new FatalModuleException("Unable to save a model", 500, null, [
                    "data" => $arrData
                ]);
            }

            return $model;
        } catch (\Exception $e) {
            throw new FatalModuleException("Unable to save a model", 500, $e, [
                "data" => $arrData
            ]);
        }
    }

    /**
     * @inheritDoc
     */
    public function delete(Model $objModel): bool
    {
        try {
            return $objModel->delete() === false;
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            throw new FatalModuleException("Unable to delete a model", 500, $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function deleteById(int $id): bool
    {
        $model = $this->getById($id);
        if (empty($model)) {
            throw new FatalModuleException("Unable to find the model by its ID", 500, null, [
                "ID" => $id
            ]);
        }

        return $this->delete($model);
    }
}
