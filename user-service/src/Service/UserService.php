<?php

namespace App\Service;

use App\Entity\User;
use App\Message\UserCreatedEvent;
use App\Model\CreateUserRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class UserService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MessageBusInterface $messageBus,
    ){
    }

    public function saveUser(CreateUserRequest $userModel): void
    {
        $user = new User();
        $user->setEmail($userModel->getEmail());
        $user->setFirstName($userModel->getFirstName());
        $user->setLastName($userModel->getLastName());

        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $this->dispatchToNotificationService($user);
    }

    private function dispatchToNotificationService(User $user): void
    {
        $event = new UserCreatedEvent($user->getEmail(), $user->getFirstName(), $user->getLastName());
        $this->messageBus->dispatch($event);
    }
}