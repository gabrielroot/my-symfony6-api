<?php

namespace App\Tests\Controller;

use App\Entity\Cooperative;
use App\Entity\Topic;
use App\Entity\Vote;
use App\Exception\BusinessRule\MemberAlreadyVotedException;
use App\Exception\BusinessRule\SessionClosedToVoteException;
use App\Exception\BusinessRule\TopicNotFromMemberCooperativeException;
use App\Repository\CooperativeRepository;
use App\Repository\TopicRepository;
use App\Repository\UserRepository;
use App\Service\TopicService;
use App\Service\VoteService;
use App\Utils\Enum\VoteChoice;
use DateTime;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class VoteControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    private VoteService $voteService;
    private TopicService $topicService;
    private CooperativeRepository $cooperativeRepository;
    private UserRepository $userRepository;
    private TopicRepository $topicRepository;
    private Vote $auxVote;

    protected function setUp(): void
    {
        $this->client = static::createClient([], ['HTTP_HOST' => $_ENV['APP_URL_BASE']]);

        self::bootKernel();
        $container = static::getContainer();

        $this->voteService = $container->get(VoteService::class);
        $this->topicService = $container->get(TopicService::class);

        $this->userRepository = $container->get(UserRepository::class);
        $this->cooperativeRepository = $container->get(CooperativeRepository::class);
        $this->topicRepository = $container->get(TopicRepository::class);

        $randUser = $this->userRepository->getOneRandom(onlyActives : true);
        $this->auxVote = (new Vote())
            ->setChoice(VoteChoice::POSITIVE)
            ->setUser($randUser)
            ->setTopic($this->topicRepository->findOneBy(['cooperative' => $randUser->getCooperative()]));

        $this->voteService->createVote($this->auxVote);
    }

    /**
     * @testdox Index: Lists the active votes.
     */
    public function testList(): void
    {
        $client = $this->client;
        $client->request('GET', '/votes/');

        $this->assertResponseIsSuccessful();

        $responseContent = $client->getResponse()->getContent();
        $votes = json_decode($responseContent, true);
        $this->assertIsArray($votes['data']);
    }

    /**
     * @testdox Show: Search for a specific vote.
     */
    public function testShow(): void
    {
        $client = $this->client;
        $client->request('GET', "/votes/{$this->auxVote->getUuid()}");

        $this->assertResponseIsSuccessful();

        $responseContent = $client->getResponse()->getContent();
        $votes = json_decode($responseContent, true);

        $this->assertIsArray($votes['data']);
        $this->assertEquals($this->auxVote->getUuid(), $votes['data']['uuid']);
    }

    /**
     * @testdox Create: Create a new vote.
     */
    public function testCreate(): void
    {
        $randUser = $this->userRepository->getOneRandom(onlyActives : true);
        $topic = (new Topic())
            ->setTitle('Topic test')
            ->setDescription('Test description')
            ->setCooperative($randUser->getCooperative());

        $this->topicService->createTopic($topic);

        $client = $this->client;
        $body = [
            'choice' => VoteChoice::POSITIVE,
            'user_uuid' => $randUser->getUuid(),
            'topic_uuid' => $topic->getUuid()];
        $client->request('POST', "/votes/", $body);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $responseContent = $client->getResponse()->getContent();
        $votes = json_decode($responseContent, true);

        $this->assertIsArray($votes['data']);
        $this->assertEquals(VoteChoice::POSITIVE, $votes['data']['choice']);
    }

    /**
     * @testdox Vote: Test MemberAlreadyVotedException throwing.
     */
    public function testMemberAlreadyVotedException(): void
    {
        $voter = $this->userRepository->getOneRandom(onlyActives : true);
        $topic = (new Topic())
            ->setTitle('Topic test')
            ->setDescription('Test description')
            ->setCooperative($voter->getCooperative());

        $this->topicService->createTopic($topic);

        $vote = (new Vote())
            ->setTopic($topic)
            ->setChoice(VoteChoice::NEGATIVE)
            ->setUser($voter);
        $this->voteService->createVote($vote);

        $client = $this->client;
        $body = [
            'choice' => VoteChoice::POSITIVE,
            'user_uuid' => $voter->getUuid(),
            'topic_uuid' => $topic->getUuid()];
        $client->request('POST', "/votes/", $body);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $responseContent = $client->getResponse()->getContent();
        $vote = json_decode($responseContent, true);

        $this->assertIsArray($vote['data']);
        $this->assertEquals((new MemberAlreadyVotedException())->getMessage(), $vote['message']);
    }

    /**
     * @testdox Vote: Test SessionClosedToVoteException throwing.
     */
    public function testSessionClosedToVoteException(): void
    {
        $voter = $this->userRepository->getOneRandom(onlyActives : true);
        $topic = (new Topic())
            ->setTitle('Topic test')
            ->setDescription('Test description')
            ->setCloseTime((new DateTime())->modify('-1 second'))
            ->setCooperative($voter->getCooperative());

        $this->topicService->createTopic($topic);

        $client = $this->client;
        $body = [
            'choice' => VoteChoice::POSITIVE,
            'user_uuid' => $voter->getUuid(),
            'topic_uuid' => $topic->getUuid()];
        $client->request('POST', "/votes/", $body);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $responseContent = $client->getResponse()->getContent();
        $vote = json_decode($responseContent, true);

        $this->assertIsArray($vote['data']);
        $this->assertEquals((new SessionClosedToVoteException())->getMessage(), $vote['message']);
    }


    /**
     * @testdox Vote: Test TopicNotFromMemberCooperativeException throwing.
     */
    public function testTopicNotFromMemberCooperativeException(): void
    {
        $cooperative = (new Cooperative())
            ->setName('Test Coop')
            ->setFantasyName('Coop Fantasy');

        $this->cooperativeRepository->save($cooperative);

        $voter = $this->userRepository->getOneRandom(onlyActives : true);
        $topic = (new Topic())
            ->setTitle('Topic test')
            ->setDescription('Test description')
            ->setCooperative($cooperative);

        $this->topicService->createTopic($topic);

        $client = $this->client;
        $body = [
            'choice' => VoteChoice::POSITIVE,
            'user_uuid' => $voter->getUuid(),
            'topic_uuid' => $topic->getUuid()];
        $client->request('POST', "/votes/", $body);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $responseContent = $client->getResponse()->getContent();
        $vote = json_decode($responseContent, true);

        $this->assertIsArray($vote['data']);
        $this->assertEquals((new TopicNotFromMemberCooperativeException())->getMessage(), $vote['message']);
    }
}