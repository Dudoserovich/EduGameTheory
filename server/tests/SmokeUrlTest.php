<?php

namespace App\Tests;

use App\Entity\User;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

// TODO: Отсутствуют проверки для путей с указанием `id`
class SmokeUrlTest extends BaseApiTest
{
    /**
     * @dataProvider urlProvider
     * @throws TransportExceptionInterface
     */
    public function testPageIsSuccessful(
        string     $method,
        string     $url,
        array|null $payload = null)
    {
        if ($url == '/api/users/{userId}') {
            $request = self::createClient()->request('GET', '/api/users');

            $user = json_decode($request->getContent())[0];
            $userId = $user->id;

            self::createClient()->request('GET', "/api/users/$userId");
        } elseif (!$payload)
            self::createClient()->request($method, $url);
        else
            self::createClient()->request($method, $url, $payload);

        self::assertResponseIsSuccessful();
    }

    public function urlProvider(): \Generator
    {
        yield ["GET", '/api/users'];
        yield ["GET", '/api/users/{userId}'];

//        yield ["POST", '/api/users', $this->payload('user_add.json')];
//        yield ["GET", '/api/users/avatar/self'];
//        yield ["GET", '/api/users/self'];
//        yield ["PUT", '/api/users/self', $this->payload('user_change.json')];
//
//        yield ["GET", '/api/terms'];
////        yield ["POST", '/api/terms', $this->payload('term_add.json')];
//
//        yield ["GET", '/api/literatures'];
//
//        yield ["GET", '/api/achievements'];
//
//        yield ["GET", '/api/topics'];
//
//        yield ["GET", '/api/tasks'];
    }
}