<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    private $userRepository;
    private UserPasswordHasherInterface $hasher;

    public function __construct(
        UserPasswordHasherInterface $hasher,
        UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
        $this->hasher = $hasher;
    }

    public function createUser(User $user, bool $flush = true): void
    {
        $user->setPassword($this->hasher->hashPassword($user, $user->getPassword()));

        $this->userRepository->save(entity: $user, flush: $flush);
    }
}