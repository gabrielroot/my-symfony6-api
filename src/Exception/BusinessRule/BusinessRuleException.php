<?php

namespace App\Exception\BusinessRule;

use Symfony\Component\HttpFoundation\Response;

class BusinessRuleException extends \LogicException
{
    public function __construct(string $message){
        parent::__construct($message, Response::HTTP_BAD_REQUEST);
    }
}