<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsDoctrineListener(event: Events::prePersist)]
class UserCreationListener
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {
    }

    private static array $passwords = [];

    public function prePersist(PrePersistEventArgs $args): void
    {
        $user = $args->getObject();

        if (!$user instanceof User) {
            return;
        }

        $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
    }
}
