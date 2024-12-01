<?php

namespace App\Exception\Integration\ViaCep;

class BadCepException extends ViaCepGlobalException
{
    public function __construct(){
        parent::__construct('O CEP informado não foi encontrado. Verifique se é um CEP válido.');
    }
}