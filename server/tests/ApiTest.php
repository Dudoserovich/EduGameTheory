<?php

namespace App\Tests;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ApiTest extends BaseApiTest
{

    /**
     * @throws TransportExceptionInterface
     */
    protected function sendRequest()
    {
        self::createClient()->request('GET', '/api/users');

        self::assertResponseIsSuccessful();
    }
}