<?php

namespace App\MessageHandler;

use App\Message\UserCreatedEvent;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UserCreatedEventHandler
{
    public function __invoke(UserCreatedEvent $event)
    {
        $logData = sprintf(
            "First Name: %s, Last Name: %s, Email: %s\n",
            $event->getFirstName(),
            $event->getLastName(),
            $event->getEmail()
        );

        $logFile = __DIR__ . '/../../var/log/user_created.log';
        file_put_contents($logFile, $logData, FILE_APPEND);
    }
}
