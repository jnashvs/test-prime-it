<?php

namespace App\Modules;



use App\Modules\Exceptions\FatalModuleException;

interface CommonModuleInterface
{
    public function getErrors(): array;

    public function registerError(array|string $message): void;

    public function hasError(): bool;

    public function throwError(): FatalModuleException;

}
