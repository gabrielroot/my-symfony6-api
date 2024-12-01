<?php

namespace App\Exception\Integration\ViaCep;

use Symfony\Component\HttpFoundation\Response;

class ViaCepGlobalException extends \LogicException
{
    public function __construct(string $message){
        parent::__construct($message, Response::HTTP_BAD_REQUEST);
    }
}