<?php

namespace App\Modules\Traits;

use App\Modules\Pagers\AbstractPager;
use App\Modules\Exceptions\FatalModuleException;
use ReflectionClass;

trait HasPager
{

    /**
     * @return AbstractPager
     * @throws FatalModuleException
     */
    public static function pager(): AbstractPager
    {
        try {
            $objReflection = new ReflectionClass(get_called_class());

            $repository = AbstractPager::PAGER_NAMESPACE . $objReflection->getShortName(
                ) . '\\' . $objReflection->getShortName() . 'Pager';
            $objPager = new $repository();
            if (empty($objPager) || !$objPager instanceof AbstractPager) {
                throw new FatalModuleException("Unable to instantiate a pager for class: " . get_called_class());
            }

            return $objPager;
        } catch (\Exception $e) {
            throw new FatalModuleException(
                "Unable to instantiate a pager for class: " . get_called_class(),
                500,
                $e
            );
        }
    }
}
