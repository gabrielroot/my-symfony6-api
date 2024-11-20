<?php

namespace App\Exception;

class MissingTopicEndTimeException extends \LogicException
{
    public function __construct(){
        parent::__construct('O horário de fechamento da sessão precisa ser definido.', 400);
    }
}