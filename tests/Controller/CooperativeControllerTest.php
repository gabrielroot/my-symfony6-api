<?php

namespace App\Tests\Controller;

use App\Entity\Cooperative;
use App\Repository\CooperativeRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class CooperativeControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    private CooperativeRepository $cooperativeRepository;

    private Cooperative $auxCooperative;

    protected function setUp(): void
    {
        $this->client = static::createClient([], ['HTTP_HOST' => $_ENV['APP_URL_BASE']]);

        self::bootKernel();
        $container = static::getContainer();

        $this->cooperativeRepository = $container->get(CooperativeRepository::class);

        $this->auxCooperative = (new Cooperative())
            ->setName('TestCooperative')
            ->setFantasyName('Test Cooperative');

        $this->cooperativeRepository->save($this->auxCooperative);
    }

    /**
     * @testdox Index: Lists the active cooperatives.
     */
    public function testList(): void
    {
        $client = $this->client;
        $client->request('GET', '/cooperatives/');

        $this->assertResponseIsSuccessful();

        $responseContent = $client->getResponse()->getContent();
        $cooperatives = json_decode($responseContent, true);
        $this->assertIsArray($cooperatives['data']);
    }

    /**
     * @testdox Show: Search for a specific cooperative.
     */
    public function testShow(): void
    {
        $client = $this->client;
        $client->request('GET', "/cooperatives/{$this->auxCooperative->getUuid()}");

        $this->assertResponseIsSuccessful();

        $responseContent = $client->getResponse()->getContent();
        $cooperatives = json_decode($responseContent, true);

        $this->assertIsArray($cooperatives['data']);
        $this->assertEquals($this->auxCooperative->getUuid(), $cooperatives['data']['uuid']);
    }

    /**
     * @testdox Delete: Delete a specific cooperative.
     */
    public function testDelete(): void
    {
        $client = $this->client;
        $client->request('DELETE', "/cooperatives/{$this->auxCooperative->getUuid()}");

        $this->assertResponseIsSuccessful();

        $responseContent = $client->getResponse()->getContent();
        $cooperatives = json_decode($responseContent, true);

        $this->assertIsArray($cooperatives['data']);
        $this->assertEquals($this->auxCooperative->getUuid(), $cooperatives['data']['uuid']);
        $this->assertFalse($cooperatives['data']['active']);
    }


    /**
     * @testdox Delete: Try to delete an already deleted (non existent) cooperative.
     */
    public function testAlreadyDeleted(): void
    {
        $client = $this->client;
        $client->request('DELETE', "/cooperatives/{$this->auxCooperative->getUuid()}");
        //Topic was already deleted
        $client->request('DELETE', "/cooperatives/{$this->auxCooperative->getUuid()}");

        $responseContent = $client->getResponse()->getContent();
        $notFoundResponse = json_decode($responseContent, true);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $this->assertFalse($notFoundResponse['success']);
    }

    /**
     * @testdox Create: Create a new cooperative.
     */
    public function testCreate(): void
    {
        $client = $this->client;
        $body = [
            'name' => 'TestCooperative2',
            'fantasyName' => "Test Cooperative 2"];
        $client->request('POST', "/cooperatives/", $body);

        $this->assertResponseIsSuccessful();

        $responseContent = $client->getResponse()->getContent();
        $cooperatives = json_decode($responseContent, true);

        $this->assertIsArray($cooperatives['data']);
        $this->assertEquals('TestCooperative2', $cooperatives['data']['name']);
    }

    /**
     * @testdox Update: Update a specific cooperative.
     */
    public function testUpdate(): void
    {
        $client = $this->client;
        $body = [
            'name' => 'TestCooperative2',
            'fantasyName' => "Test Cooperative 2"];
        $client->request('PUT', "/cooperatives/{$this->auxCooperative->getUuid()}", $body);

        $this->assertResponseIsSuccessful();

        $responseContent = $client->getResponse()->getContent();
        $cooperatives = json_decode($responseContent, true);

        $this->assertIsArray($cooperatives['data']);
        $this->assertEquals('TestCooperative2', $cooperatives['data']['name']);
    }
}