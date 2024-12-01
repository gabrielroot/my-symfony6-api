<?php

namespace App\Exception\BusinessRule;

class SessionClosedToVoteException extends BusinessRuleException
{
    public function __construct(){
        parent::__construct('Esta pauta não aceita mais votos, pois já se encerrou.');
    }
}