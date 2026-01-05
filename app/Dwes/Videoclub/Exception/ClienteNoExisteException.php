<?php

namespace Dwes\Videoclub\Exception;

class ClienteNoExisteException extends VideoclubException
{
    public function __construct($message = "", $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}