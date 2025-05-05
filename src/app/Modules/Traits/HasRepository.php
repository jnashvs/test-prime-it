<?php

namespace App\Modules\Traits;

use App\Modules\Repositories\AbstractRepository;
use App\Modules\Exceptions\FatalModuleException;
use Illuminate\Database\Eloquent\Factories\Factory;
use ReflectionClass;

trait HasRepository
{

    /**
     * @return AbstractRepository
     * @throws FatalModuleException
     */
    public static function repository(): AbstractRepository
    {
        try {
            $objReflection = new ReflectionClass(get_called_class());

            $repository = AbstractRepository::REPO_NAMESPACE . $objReflection->getShortName(
                ) . '\\' . $objReflection->getShortName() . 'Repository';
            $objRepository = new $repository();
            if (empty($objRepository) || !$objRepository instanceof AbstractRepository) {
                throw new FatalModuleException("Unable to instantiate a repository for class: " . get_called_class());
            }

            return $objRepository;
        } catch (\Exception $e) {
            throw new FatalModuleException(
                "Unable to instantiate a repository for class: " . get_called_class(),
                500,
                $e
            );
        }
    }
}
