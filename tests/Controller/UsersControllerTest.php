<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UsersControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient([], ['HTTP_HOST' => $_ENV['APP_URL_BASE']]);
    }

    /**
     * @testdox Index: Lists the active users.
     */
    public function testList(): void
    {
        $client = $this->client;
        $client->request('GET', '/users/');

        $this->assertResponseIsSuccessful();

        $responseContent = $client->getResponse()->getContent();
        $users = json_decode($responseContent, true);
        $this->assertIsArray($users['data']);
    }

    /**
     * @testdox Show: Search for a specific user.
     */
    public function testShow(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $userService = $container->get(UserService::class);
        $dbUser = $userService->find(1);

        $client = $this->client;
        $client->request('GET', "/users/{$dbUser->getUuid()}");

        $this->assertResponseIsSuccessful();

        $responseContent = $client->getResponse()->getContent();
        $users = json_decode($responseContent, true);

        $this->assertIsArray($users['data']);
        $this->assertEquals($dbUser->getUuid(), $users['data']['uuid']);
    }

    /**
     * @testdox Delete: Delete a specific user.
     */
    public function testDelete(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $userService = $container->get(UserService::class);
        $dbUser = $userService->find(1);

        $client = $this->client;
        $client->request('DELETE', "/users/{$dbUser->getUuid()}");

        $this->assertResponseIsSuccessful();

        $responseContent = $client->getResponse()->getContent();
        $users = json_decode($responseContent, true);

        $this->assertIsArray($users['data']);
        $this->assertEquals($dbUser->getUuid(), $users['data']['uuid']);
        $this->assertFalse($users['data']['active']);

        //User was already deleted
        $client->request('DELETE', "/users/{$dbUser->getUuid()}");

        $responseContent = $client->getResponse()->getContent();
        $notFoundResponse = json_decode($responseContent, true);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $this->assertFalse($notFoundResponse['success']);
    }

    /**
     * @testdox Create: Create a new user.
     */
    public function testCreate(): void
    {
        $client = $this->client;
        $body = ['username' => 'gabrielroot', 'name' => "Gabriel"];
        $client->request('POST', "/users/", $body);

        $this->assertResponseIsSuccessful();

        $responseContent = $client->getResponse()->getContent();
        $users = json_decode($responseContent, true);

        $this->assertIsArray($users['data']);
        $this->assertEquals('gabrielroot', $users['data']['username']);

        //User already exists
        $client->request('POST', "/users/", $body);

        $responseContent = $client->getResponse()->getContent();
        $badRequest = json_decode($responseContent, true);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertFalse($badRequest['success']);
    }

    /**
     * @testdox Update: Update a specific user.
     */
    public function testUpdate(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $userService = $container->get(UserService::class);
        $dbUser = $userService->find(id: 1, onlyActive: false);

        $client = $this->client;
        $body = ['username' => 'gabrielroot', 'name' => "Gabriel"];
        $client->request('PUT', "/users/{$dbUser->getUuid()}", $body);

        $this->assertResponseIsSuccessful();

        $responseContent = $client->getResponse()->getContent();
        $users = json_decode($responseContent, true);

        $this->assertIsArray($users['data']);
        $this->assertEquals('gabrielroot', $users['data']['username']);

        //Trying to change the username to another existing username
        $dbUser = $userService->find(id: 2, onlyActive: false);
        $client->request('PUT', "/users/{$dbUser->getUuid()}", $body);

        $responseContent = $client->getResponse()->getContent();
        $badRequest = json_decode($responseContent, true);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertFalse($badRequest['success']);
    }
}