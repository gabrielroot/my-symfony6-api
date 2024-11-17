<?php

namespace App\Service;

use App\Entity\User;
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
     * @param User $user
     * @param bool $flush
     * @return void
     * @throws UniqueConstraintViolationException
     */
    public function createUser(User $user, bool $flush = true): void
    {
        $this->save(entity: $user, flush: $flush);
    }
}