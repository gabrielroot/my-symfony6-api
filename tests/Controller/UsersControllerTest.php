<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\CooperativeRepository;
use App\Repository\UserRepository;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class UsersControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    private UserService $userService;
    private CooperativeRepository $cooperativeRepository;
    private User $auxUser;

    protected function setUp(): void
    {
        $this->client = static::createClient([], ['HTTP_HOST' => $_ENV['APP_URL_BASE']]);

        self::bootKernel();
        $container = static::getContainer();

        $this->userService = $container->get(UserService::class);
        $this->cooperativeRepository = $container->get(CooperativeRepository::class);

        $this->auxUser = (new User())
            ->setName('TestUser')
            ->setUsername('test_user')
            ->setCooperative($this->cooperativeRepository->getOneRandom(onlyActives : true));

        $this->userService->save($this->auxUser);
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
        $client = $this->client;
        $client->request('GET', "/users/{$this->auxUser->getUuid()}");

        $this->assertResponseIsSuccessful();

        $responseContent = $client->getResponse()->getContent();
        $users = json_decode($responseContent, true);

        $this->assertIsArray($users['data']);
        $this->assertEquals($this->auxUser->getUuid(), $users['data']['uuid']);
    }

    /**
     * @testdox Delete: Delete a specific user.
     */
    public function testDelete(): void
    {
        $client = $this->client;
        $client->request('DELETE', "/users/{$this->auxUser->getUuid()}");

        $this->assertResponseIsSuccessful();

        $responseContent = $client->getResponse()->getContent();
        $users = json_decode($responseContent, true);

        $this->assertIsArray($users['data']);
        $this->assertEquals($this->auxUser->getUuid(), $users['data']['uuid']);
        $this->assertFalse($users['data']['active']);
    }

    /**
     * @testdox Delete: Try to delete an already deleted (non existent) user.
     */
    public function testAlreadyDeleted(): void
    {
        $client = $this->client;
        $client->request('DELETE', "/users/{$this->auxUser->getUuid()}");
        //Topic was already deleted
        $client->request('DELETE', "/users/{$this->auxUser->getUuid()}");

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
        $body = [
            'username' => 'gabrielroot',
            'name' => "Gabriel",
            'cooperative_uuid' => $this->cooperativeRepository->getOneRandom(onlyActives: true)->getUuid()];
        $client->request('POST', "/users/", $body);

        $this->assertResponseIsSuccessful();

        $responseContent = $client->getResponse()->getContent();
        $users = json_decode($responseContent, true);

        $this->assertIsArray($users['data']);
        $this->assertEquals('gabrielroot', $users['data']['username']);
    }

    /**
     * @testdox Create: Trying to duplicate an user.
     */
    public function testCreatingDuplicated(): void
    {
        $client = $this->client;
        $body = [
            'username' => 'gabrielroot',
            'name' => "Gabriel",
            'cooperative_uuid' => $this->cooperativeRepository->getOneRandom(onlyActives: true)->getUuid()];
        $client->request('POST', "/users/", $body);
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
        $client = $this->client;
        $body = [
            'username' => 'gabrielroot',
            'name' => "Gabriel",
            'cooperative_uuid' => $this->auxUser->getCooperative()->getUuid()];
        $client->request('PUT', "/users/{$this->auxUser->getUuid()}", $body);

        $this->assertResponseIsSuccessful();

        $responseContent = $client->getResponse()->getContent();
        $users = json_decode($responseContent, true);

        $this->assertIsArray($users['data']);
        $this->assertEquals('gabrielroot', $users['data']['username']);

        //Trying to change the username to another existing username
        $existingUser = (new User())
            ->setName('TestUser')
            ->setUsername('test_user')
            ->setCooperative($this->cooperativeRepository->getOneRandom(onlyActives : true));
        $this->userService->save($existingUser);

        $client->request('PUT', "/users/{$existingUser->getUuid()}", $body);

        $responseContent = $client->getResponse()->getContent();
        $badRequest = json_decode($responseContent, true);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertFalse($badRequest['success']);
    }
}