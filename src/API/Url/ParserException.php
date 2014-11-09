<?php

namespace GroupByInc\API\Url;

use Exception;

class ParserException extends Exception
{
    public function __construct($message, Exception $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}