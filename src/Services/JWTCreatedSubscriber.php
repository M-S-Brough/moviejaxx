<?php

namespace App\Services;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JWTCreatedSubscriber implements EventSubscriberInterface
{
    // Define the events to which this subscriber listens
    public static function getSubscribedEvents() : array
    {
        return [
            // Listen to the 'lexik_jwt_authentication.on_jwt_created' event
            'lexik_jwt_authentication.on_jwt_created' => 'onJWTCreated',
        ];
    }

    // Callback function called when the 'lexik_jwt_authentication.on_jwt_created' event is dispatched
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        // Get the user associated with the JWT token
        $user = $event->getUser();
        // Get the current payload data
        $payload = $event->getData();

        // Add custom data to the payload
        $payload['username'] = $user->getEmail(); // Add the user's email as 'username'
        $payload['roles'] = $user->getRoles(); // Add the user's roles

        // Set the updated payload data
        $event->setData($payload);
    }
}
