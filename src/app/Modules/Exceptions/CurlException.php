<?php


namespace App\Modules\Exceptions;


use Throwable;

class CurlException extends \Exception
{

    private ?array $context;

    /**
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     * @param array|null     $context
     */
    public function __construct(
        $message = "",
        $code = 0,
        Throwable $previous = null,
        array $context = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
    }

    /**
     * @return array|null
     */
    public function getContext(): ?array
    {
        return $this->context;
    }

    /**
     * @param array|null $context
     */
    public function setContext(?array $context): void
    {
        $this->context = $context;
    }
}
