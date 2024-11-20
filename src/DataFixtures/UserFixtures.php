<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserService;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends BaseFixtures
{
    private UserService $userService;

    private UserRepository $userRepository;

    public function __construct(
        UserService $userService,
        UserRepository $userRepository)
    {
        parent::__construct();
        $this->userService = $userService;
        $this->userRepository = $userRepository;
    }
    public function load(ObjectManager $manager): void
    {
        $this->createUsers();

        $manager->flush();
        $manager->flush();
    }

    private function createUsers(): void
    {
        for($i = 0; $i < 100; $i++) {
            $user = new User();
            $user
                ->setName($this->generateName())
                ->setUsername($this->generateUsername());

            $this->setRandomDelete($user);

            $this->userService->createUser(user: $user, flush: false);
        }
    }
}
