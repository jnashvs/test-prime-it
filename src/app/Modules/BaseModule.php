<?php

namespace App\Modules;



use App\Modules\Exceptions\FatalModuleException;

/**
 *
 */
class BaseModule implements CommonModuleInterface
{
    /**
     * @var array
     */
    private array $errors = [];

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param $message
     * @return void
     * @throws FatalModuleException
     */
    public function registerError(array|string $message): void{
        if(is_array($message)){
            foreach($message as $item){
                $this->errors[] = $item;
            }
        }
        else {
            $this->errors[] = $message;
        }
    }

    /**
     * @return bool
     */
    public function hasError(): bool{
        return count($this->errors) > 0;
    }


    /**
     * @return FatalModuleException
     * @throws FatalModuleException
     */
    public function throwError(): FatalModuleException{
        if($this->hasError()) {
            throw new FatalModuleException($this->getErrors());
        }
    }

}
