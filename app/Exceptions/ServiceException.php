<?php

namespace App\Exceptions;

use Exception;

class ServiceException extends Exception
{
    protected $isJson;

    public function __construct($message, $code = 400, $isJson = false)
    {
        $this->isJson = $isJson;

        parent::__construct($message, $code);
    }

    public function isJson()
    {
        return $this->isJson ? true : false;
    }
}