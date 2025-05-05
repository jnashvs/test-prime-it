<?php

namespace App\Modules\Repositories;

use App\Modules\Exceptions\FatalModuleException;
use Illuminate\Database\Eloquent\Model;

interface RepositoryInterface
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
     * @return Model[]
     */
    public function getAll(array $arrRelations = []): array;

    /**
     * Updates the model by the data
     *
     * @param $model
     * @param array $arrData
     * @return Model
     * @throws FatalModuleException
     */
    public function update($model, array $arrData): Model;

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
