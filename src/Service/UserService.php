<?php

namespace App\Service;

use App\Entity\User;
use App\Integration\ViaCep\ViaCepIntegration;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class UserService extends AbstractService
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        parent::__construct($userRepository);
        $this->userRepository = $userRepository;
    }

    /**
     * @param User $entity
     * @param bool $flush
     * @return void
     */
    public function createUser(User $user, bool $flush = true): void
    {
        $this->validateZipCode($user);
        $this->userRepository->save($user, $flush);
    }

    public function validateZipCode(User $user): void
    {
        $viaCep = new ViaCepIntegration();
        $addressResponse = $viaCep->getZipCode($user->getAddress()->getZipCode());
        die(dump($addressResponse));
    }
}