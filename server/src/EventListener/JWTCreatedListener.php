<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\User;
use Symfony\Component\HttpFoundation\RequestStack;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class JWTCreatedListener
{
    private RequestStack $requestStack;
    private HubInterface $hub;

    public function __construct(RequestStack $requestStack, HubInterface $hub)
    {
        $this->requestStack = $requestStack;
        $this->hub = $hub;
    }

    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($request === null) {
            return;
        }

        $payload = $event->getData();
        $payload['ip'] = $request->getClientIp();

        $user = $event->getUser();
        if (!$user instanceof User) {
            return;
        }

        //$payload['id'] = $user->toIdentity();
        //$payload['fullname'] = $user->toFullname()->toString();

        $event->setData($payload);

        $header        = $event->getHeader();
        $header['cty'] = 'JWT';

        $event->setHeader($header);

        $update = new Update(
            "/auth/{$user->getUsername()}",
            json_encode(['message' => "Вы успешно авторизовались как {$user->getFio()}"])
        );

        $this->hub->publish($update);
    }
}