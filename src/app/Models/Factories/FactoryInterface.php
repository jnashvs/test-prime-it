<?php

namespace App\Models\Factories;

use App\Modules\Exceptions\FatalModuleException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface FactoryInterface
{

    /**
     * Get a database model by its ID
     *
     * @param int $id
     * @param String[] $arrRelations
     * @return Model|null
     */
    public function getById(int $id, array $arrRelations = []): ?Model;

    /**
     * @param array $arrRelations
     * @return Collection
     */
    public function getAll(array $arrRelations = []): Collection;

    /**
     * Updates the model by the data
     *
     * @param $model
     * @param array $arrData
     * @return Model
     * @throws FatalModuleException
     */
    public function updateByModel($model, array $arrData): Model;

    /**
     * Removes record from the database by the model
     *
     * @param Model $objModel
     * @return bool
     * @throws FatalModuleException
     */
    public function delete(Model $objModel): bool;

    /**
     * Removes a record by its ID
     *
     * @param int $id
     * @return bool
     * @throws FatalModuleException
     */
    public function deleteById(int $id): bool;
}
