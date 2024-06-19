<?php

namespace Tests;

use App\Message\UserCreatedEvent;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserCreatedEventHandlerFunctionalTest extends KernelTestCase
{
    private $messageBus;
    private $notificationTransport;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->messageBus = self::$container->get('message_bus');

        $this->notificationTransport = self::$container->get('messenger.transport.notifications');
        $this->notificationTransport->reset();
    }

    public function testHandleUserCreatedEvent()
    {
        $event = new UserCreatedEvent('test@example.com', 'John', 'Doe');

        $this->messageBus->dispatch($event);

        $this->assertCount(1, $this->notificationTransport->getSent());
        $envelope = $this->notificationTransport->getSent()[0];
        $this->assertInstanceOf(UserCreatedEvent::class, $envelope->getMessage());
        $this->assertEquals('test@example.com', $envelope->getMessage()->getEmail());
        $this->assertEquals('John', $envelope->getMessage()->getFirstName());
        $this->assertEquals('Doe', $envelope->getMessage()->getLastName());

        $logFile = sys_get_temp_dir() . '/user_created.log';
        $loggedData = file_get_contents($logFile);
        $expectedData = "First Name: John, Last Name: Doe, Email: test@example.com\n";
        $this->assertStringContainsString($expectedData, $loggedData);
    }
}