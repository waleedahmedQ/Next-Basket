<?php

namespace Tests;
use App\Entity\User;
use App\Message\UserCreatedEvent;
use App\Model\CreateUserRequest;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\Transport\InMemory\InMemoryTransport as BaseInMemoryTransport;

/**
 * @property $container
 */
class UserServiceFunctionalTest extends KernelTestCase
{
    private $entityManager;
    private  BaseInMemoryTransport $messageBus;
    private $notificationTransport;

    protected function setUp(): void
    {
        // Boot the Symfony kernel
        self::bootKernel();

        // Get EntityManager from the container
        $this->entityManager = self::$container->get('doctrine')->getManager();

        // Get MessageBusInterface from the container
        $this->messageBus = self::$container->get('message_bus');

        // Reset the in-memory transport for notifications
        $this->notificationTransport = self::$container->get('messenger.transport.notifications');
        $this->notificationTransport->reset();
    }

    public function testSaveUser()
    {
        // Create a new user request
        $userModel = new CreateUserRequest();
        $userModel->setEmail('test@example.com');
        $userModel->setFirstName('John');
        $userModel->setLastName('Doe');

        // Call the UserService to save the user
        $userService = self::$container->get('App\Service\UserService');
        $userService->saveUser($userModel);

        // Assert that the user was persisted in the database
        $userRepository = $this->entityManager->getRepository(User::class);
        $user = $userRepository->findOneBy(['email' => 'test@example.com']);
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John', $user->getFirstName());
        $this->assertEquals('Doe', $user->getLastName());

        // Assert that the notification event was dispatched
        $this->assertCount(1, $this->notificationTransport->getSent());
        $envelope = $this->notificationTransport->getSent()[0];
        $this->assertInstanceOf(UserCreatedEvent::class, $envelope->getMessage());
        $this->assertEquals('test@example.com', $envelope->getMessage()->getEmail());
        $this->assertEquals('John', $envelope->getMessage()->getFirstName());
        $this->assertEquals('Doe', $envelope->getMessage()->getLastName());
    }
}