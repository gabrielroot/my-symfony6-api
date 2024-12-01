<?php

namespace App\Exception\BusinessRule;

class MemberAlreadyVotedException extends BusinessRuleException
{
    public function __construct(){
        parent::__construct('Este membro já votou nesta mesma pauta.');
    }
}