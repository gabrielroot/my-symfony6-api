<?php

namespace App\Exception;

class MemberAlreadyVotedException extends \LogicException
{
    public function __construct(){
        parent::__construct('Este membro já votou nesta mesma pauta.', 400);
    }
}