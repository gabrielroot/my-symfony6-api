<?php

namespace App\Exception;

class SessionClosedToVoteException extends \LogicException
{
    public function __construct(){
        parent::__construct('Esta pauta não aceita mais votos, pois já se encerrou.', 400);
    }
}