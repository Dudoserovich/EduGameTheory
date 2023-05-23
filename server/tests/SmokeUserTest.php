<?php

namespace App\Tests;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class SmokeUserTest extends BaseApiTest
{
    /**
     * @throws TransportExceptionInterface
     */
    public function testGetUsers()
    {
        self::createClient()->request(
            'GET',
            '/api/users'
        );
        self::assertResponseIsSuccessful();
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testPostUser()
    {
        self::createClient()->request(
            'POST',
            '/api/users',
            $this->payload('user_add.json')
        );
        self::assertResponseIsSuccessful();
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testGetSelfAvatar()
    {
        self::createClient()->request(
            'GET',
            '/api/users/self/avatar'
        );
        self::assertResponseIsSuccessful();
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testGetAvatars()
    {
        self::createClient()->request(
            'GET',
            '/api/users/avatars'
        );
        self::assertResponseIsSuccessful();
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testGetInfo()
    {
        self::createClient()->request(
            'GET',
            '/api/users/self'
        );
        self::assertResponseIsSuccessful();
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testPutUser()
    {
        self::createClient()->request(
            'PUT',
            '/api/users/self',
            $this->payload('user_change.json')
        );
        self::assertResponseIsSuccessful();
    }
}