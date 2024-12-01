<?php

namespace App\Exception\BusinessRule;

class TopicNotFromMemberCooperativeException extends BusinessRuleException
{
    public function __construct(){
        parent::__construct('Esta pauta não foi criada pela cooperativa deste membro.');
    }
}