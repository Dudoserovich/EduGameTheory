<?php

namespace App\Tests;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

// TODO: Отсутствуют проверки для путей с указанием `id`
class SmokeUrlTest extends BaseApiTest
{
    /**
     * @dataProvider urlProvider
     * @throws TransportExceptionInterface
     */
    public function testPageIsSuccessful(
        string $method,
        string $url,
        array|null $payload = null)
    {
        if (!$payload)
            self::createClient()->request($method, $url);
        else
            self::createClient()->request($method, $url, $payload);

        self::assertResponseIsSuccessful();
    }

    public function urlProvider(): \Generator
    {
        yield ["GET", '/api/users'];
//        yield ["POST", '/api/users', $this->payload('user_add.json')];
        yield ["GET", '/api/users/self/avatar'];
        yield ["GET", '/api/users/avatars'];
        yield ["GET", '/api/users/self'];
        yield ["PUT", '/api/users/self', $this->payload('user_change.json')];

        yield ["GET", '/api/terms'];
//        yield ["POST", '/api/terms', $this->payload('term_add.json')];

        yield ["GET", '/api/literatures'];

        yield ["GET", '/api/achievements'];

        yield ["GET", '/api/topics'];

        yield ["GET", '/api/tasks'];
    }
}