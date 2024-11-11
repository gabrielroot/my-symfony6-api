<?php

namespace App\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class AuthenticationListener
{
    public function __construct(
        readonly private EntityManagerInterface $em)
    {}

    public function onAuthenticationSuccess(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();
        $user->setLastLogin(new \DateTime());
        $this->em->getRepository(User::class)->save($user);
    }
}