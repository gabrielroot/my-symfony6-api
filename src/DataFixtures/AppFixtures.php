<?php

namespace App\DataFixtures;

use App\Entity\Cooperative;
use App\Entity\Topic;
use App\Entity\User;
use App\Entity\Vote;
use App\Service\CooperativeService;
use App\Service\TopicService;
use App\Service\UserService;
use App\Service\VoteService;
use App\Utils\Enum\VoteChoice;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends BaseFixtures
{
    private UserService $userService;
    private CooperativeService $cooperativeService;
    private TopicService $topicService;
    private VoteService $voteService;

    private $cooperatives;
    private $topics;
    private $users;

    public function __construct(
        UserService $userService,
        CooperativeService $cooperativeService,
        TopicService $topicService,
        VoteService $voteService)
    {
        parent::__construct();
        $this->userService = $userService;
        $this->cooperativeService = $cooperativeService;
        $this->topicService = $topicService;
        $this->voteService = $voteService;

        $this->cooperatives = [$this->createOneCooperative(), $this->createOneCooperative(), $this->createOneCooperative()];
        $this->users = [$this->createOneUser(), $this->createOneUser(), $this->createOneUser()];
        $this->topics = [$this->createOneTopic(), $this->createOneTopic(), $this->createOneTopic()];

    }

    public function load(ObjectManager $manager): void
    {
        $this->createCooperatives();
        $this->createUsers();
        $this->createTopics();
        $this->createVotes();

        $manager->flush();
    }

    private function createOneCooperative(): Cooperative
    {
        $cooperative = new Cooperative();
        $cooperative
            ->setName($this->generateCooperativeName())
            ->setFantasyName($this->generateCooperativeName());

        $this->setRandomDelete($cooperative);

        $this->cooperativeService->save(entity: $cooperative, flush: false);

        return $cooperative;
    }

    private function createCooperatives(): void
    {
        for($i = 0; $i < 10; $i++) {
            $this->createOneCooperative();
        }
    }

    private function createOneUser(): User
    {
        $user = new User();
        $user
            ->setName($this->generateName())
            ->setUsername($this->generateUsername())
            ->setCooperative($this->cooperatives[array_rand($this->cooperatives)]);

        $this->setRandomDelete($user);

        $this->userService->save(entity: $user, flush: false);

        return $user;
    }

    private function createUsers(): void
    {
        for($i = 0; $i < 100; $i++) {
            $this->createOneUser();
        }
    }

    private function createOneTopic(): Topic
    {
        $topic = new Topic();
        $topic
            ->setTitle($this->places[array_rand($this->places)])
            ->setDescription($this->places[array_rand($this->places)])
            ->setCloseTime(new \DateTime(rand(2024, 2025) . '-' . rand(1, 12) . '-' . rand(1, 28)))
            ->setCooperative($this->cooperatives[array_rand($this->cooperatives)]);

        $this->setRandomDelete($topic);

        $this->topicService->save(entity: $topic, flush: false);

        return $topic;
    }
    private function createTopics(): void
    {
        for($i = 0; $i < 50; $i++) {
            $this->createOneTopic();
        }
    }

    public function createVotes()
    {
        foreach ($this->topics as $topic) {
            for($i = 0; $i < 50; $i++) {
                $vote = new Vote();
                $vote
                    ->setChoice([VoteChoice::POSITIVE, VoteChoice::NEGATIVE][rand(0, 1)])
                    ->setTopic($topic)
                    ->setUser($this->users[array_rand($this->users)]);
                    $this->voteService->save($vote);
            }
        }
    }
}
