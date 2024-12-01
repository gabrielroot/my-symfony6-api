<?php

namespace App\Exception\BusinessRule;

class MissingTopicEndTimeException extends BusinessRuleException
{
    public function __construct(){
        parent::__construct('O horário de fechamento da sessão precisa ser definido.');
    }
}