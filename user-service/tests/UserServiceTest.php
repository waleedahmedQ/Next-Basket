<?php

namespace Tests;

use App\Entity\User;
use App\Message\UserCreatedEvent;
use App\Model\CreateUserRequest;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;


class UserServiceTest extends TestCase
{
    private $entityManager;
    private $messageBus;
    private $userService;

    protected function setUp(): void
    {
        // Mock EntityManager
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        // Mock MessageBusInterface
        $this->messageBus = $this->createMock(MessageBusInterface::class);

        // Create UserService instance with mocked dependencies
        $this->userService = new UserService($this->entityManager, $this->messageBus);
    }

    public function testSaveUser()
    {
        // Test data
        $email = 'test@example.com';
        $firstName = 'John';
        $lastName = 'Doe';

        // Mock CreateUserRequest
        $userModel = new CreateUserRequest();
        $userModel->setEmail($email);
        $userModel->setFirstName($firstName);
        $userModel->setLastName($lastName);

        // Expect EntityManager to persist and flush the User entity
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->callback(function ($user) use ($email, $firstName, $lastName) {
                return $user instanceof User &&
                    $user->getEmail() === $email &&
                    $user->getFirstName() === $firstName &&
                    $user->getLastName() === $lastName;
            }));
        $this->entityManager->expects($this->once())
            ->method('flush');

        // Expect MessageBus to dispatch UserCreatedEvent
        $this->messageBus->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function ($event) use ($email, $firstName, $lastName) {
                return $event instanceof UserCreatedEvent &&
                    $event->getEmail() === $email &&
                    $event->getFirstName() === $firstName &&
                    $event->getLastName() === $lastName;
            }))
            ->willReturn(new Envelope(new \stdClass()));

        // Call the service method
        $this->userService->saveUser($userModel);
    }
}
