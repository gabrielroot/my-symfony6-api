<?php

namespace App\Exception;

class TopicNotFromMemberCooperativeException extends \LogicException
{
    public function __construct(){
        parent::__construct('Esta pauta não foi criada pela cooperativa deste membro.', 400);
    }
}