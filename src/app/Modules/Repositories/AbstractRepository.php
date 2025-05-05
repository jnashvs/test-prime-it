<?php

namespace App\Modules\Repositories;

use App\Modules\Exceptions\FatalModuleException;
use Illuminate\Database\Eloquent\Model;

/**
 * Abstract repository to handle Model queries
 */
abstract class AbstractRepository implements RepositoryInterface
{

    public const REPO_NAMESPACE = "App\\Modules\\Repositories\\";

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
    public function getById(int $id, array $arrRelations = [], string $dto = null): ?Model
    {
        $objBuilder = $this->model::query()->where('id', '=', $id);

        foreach ($arrRelations as $relation) {
            $objBuilder->with($relation);
        }

        return $objBuilder->first();
    }

    /**
     * @inheritDoc
     */
    public function getAll(array $arrRelations = []): array
    {
        $objBuilder = $this->model::query();
        foreach ($arrRelations as $relation) {
            $objBuilder->with($relation);
        }

        return $objBuilder->get()->toArray();
    }

    /**
     * @inheritDoc
     */
    public function update($model, array $arrData): Model
    {
        try {
            $result = $model->update($arrData);
            if (empty($result)) {
                throw new FatalModuleException("Unable to save a model", 500, null, [
                    "data" => $arrData
                ]);
            }

            return $model;
        } catch(\Exception $e) {
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
            if($objModel->delete() === false) {
                return false;
            }

            return true;
        } catch(\Exception $e) {
            throw new FatalModuleException("Unable to delete a model", 500, $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function deleteById(int $id): bool
    {
        $model = $this->getById($id);
        if(empty($model)) {
            throw new FatalModuleException("Unable to find the model by its ID", 500, null, [
                "ID" => $id
            ]);
        }

        return $this->delete($model);
    }
}
