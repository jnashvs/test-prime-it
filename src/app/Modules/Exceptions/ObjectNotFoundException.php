<?php

namespace App\Modules\Exceptions;

use JetBrains\PhpStorm\Internal\LanguageLevelTypeAware;

class ObjectNotFoundException extends \Exception
{

  public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
  {
    $this->message = "The requested object not found";
    parent::__construct($message, $code, $previous);
  }

}
