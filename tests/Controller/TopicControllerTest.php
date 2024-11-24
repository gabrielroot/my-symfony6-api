<?php

namespace App\Tests\Controller;

use App\Entity\Topic;
use App\Exception\MissingTopicEndTimeException;
use App\Repository\CooperativeRepository;
use App\Service\TopicService;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TopicControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    private TopicService $topicService;
    private CooperativeRepository $cooperativeRepository;
    private Topic $auxTopic;

    protected function setUp(): void
    {
        $this->client = static::createClient([], ['HTTP_HOST' => $_ENV['APP_URL_BASE']]);

        self::bootKernel();
        $container = static::getContainer();

        $this->topicService = $container->get(TopicService::class);
        $this->cooperativeRepository = $container->get(CooperativeRepository::class);

        $this->auxTopic = (new Topic())
            ->setTitle('TestTopic')
            ->setDescription('test_topic with a short description')
            ->setCooperative($this->cooperativeRepository->getOneRandom(onlyActives : true));

        $this->topicService->createTopic($this->auxTopic);
    }

    /**
     * @testdox Index: Lists the active topics.
     */
    public function testList(): void
    {
        $client = $this->client;
        $client->request('GET', '/topics/');

        $this->assertResponseIsSuccessful();

        $responseContent = $client->getResponse()->getContent();
        $topics = json_decode($responseContent, true);
        $this->assertIsArray($topics['data']);
    }

    /**
     * @testdox Show: Search for a specific topic.
     */
    public function testShow(): void
    {
        $client = $this->client;
        $client->request('GET', "/topics/{$this->auxTopic->getUuid()}");

        $this->assertResponseIsSuccessful();

        $responseContent = $client->getResponse()->getContent();
        $topics = json_decode($responseContent, true);

        $this->assertIsArray($topics['data']);
        $this->assertEquals($this->auxTopic->getUuid(), $topics['data']['uuid']);
    }

    /**
     * @testdox Delete: Delete a specific topic.
     */
    public function testDelete(): void
    {
        $client = $this->client;
        $client->request('DELETE', "/topics/{$this->auxTopic->getUuid()}");

        $this->assertResponseIsSuccessful();

        $responseContent = $client->getResponse()->getContent();
        $topics = json_decode($responseContent, true);

        $this->assertIsArray($topics['data']);
        $this->assertEquals($this->auxTopic->getUuid(), $topics['data']['uuid']);
        $this->assertFalse($topics['data']['active']);
    }

    /**
     * @testdox Delete: Try to delete an already deleted (non existent) topic.
     */
    public function testAlreadyDeleted(): void
    {
        $client = $this->client;
        $client->request('DELETE', "/topics/{$this->auxTopic->getUuid()}");
        //Topic was already deleted
        $client->request('DELETE', "/topics/{$this->auxTopic->getUuid()}");

        $responseContent = $client->getResponse()->getContent();
        $notFoundResponse = json_decode($responseContent, true);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $this->assertFalse($notFoundResponse['success']);
    }

    /**
     * @testdox Create: Create a new topic.
     */
    public function testCreate(): void
    {
        $client = $this->client;
        $body = [
            'title' => 'Topic title',
            'description' => "Topic description",
            'cooperative_uuid' => $this->cooperativeRepository->getOneRandom(onlyActives: true)->getUuid()];
        $client->request('POST', "/topics/", $body);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $responseContent = $client->getResponse()->getContent();
        $topics = json_decode($responseContent, true);

        $this->assertIsArray($topics['data']);
        $this->assertEquals('Topic title', $topics['data']['title']);
    }

    /**
     * @testdox Update: Update a specific topic.
     */
    public function testUpdate(): void
    {
        $client = $this->client;
        $body = [
            'title' => 'Topic title',
            'description' => "Topic description",
            'closeTime' => "2024-11-21T16:03:00",
            'cooperative_uuid' => $this->cooperativeRepository->getOneRandom(onlyActives: true)->getUuid()];
        $client->request('PUT', "/topics/{$this->auxTopic->getUuid()}", $body);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseContent = $client->getResponse()->getContent();
        $topics = json_decode($responseContent, true);

        $this->assertIsArray($topics['data']);
        $this->assertEquals('Topic title', $topics['data']['title']);
    }

    /**
     * @testdox Update: Test MissingTopicEndTimeException throwing.
     */
    public function testMissingTopicEndTimeException(): void
    {
        $client = $this->client;
        $body = [
            'title' => 'Topic title',
            'description' => "Topic description",
            'cooperative_uuid' => $this->cooperativeRepository->getOneRandom(onlyActives: true)->getUuid()];
        $client->request('PUT', "/topics/{$this->auxTopic->getUuid()}", $body);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $responseContent = $client->getResponse()->getContent();
        $topic = json_decode($responseContent, true);

        $this->assertIsArray($topic['data']);
        $this->assertEquals((new MissingTopicEndTimeException())->getMessage(), $topic['message']);
    }
}