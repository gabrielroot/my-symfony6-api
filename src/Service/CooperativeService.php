<?php

namespace App\Service;

use App\Repository\CooperativeRepository;

class CooperativeService extends AbstractService
{
    private $cooperativeRepository;

    public function __construct(CooperativeRepository $cooperativeRepository)
    {
        parent::__construct($cooperativeRepository);
        $this->cooperativeRepository = $cooperativeRepository;
    }
}