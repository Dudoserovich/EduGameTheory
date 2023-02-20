<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;


abstract class BaseApiTest extends ApiTestCase
{
    private static ?string $token = null;

    abstract protected function sendRequest();

    protected static function createClient(array $kernelOptions = [], array $defaultOptions = []): Client
    {
        return parent::createClient([], ['headers' => ['Authorization' => "Bearer " . self::$token]]);
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        $response = static::createClient()->request('POST', '/api/login_check', [
            'headers' => ['Content-Type: application/json'],
            'body' => json_encode(['username' => 'admin', 'password' => 'admin'])
        ]);

        self::$token = json_decode($response->getContent(), true)['token'] ?? null;
    }
}
