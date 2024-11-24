<?php

namespace App\Service;

use App\Entity\Topic;
use App\Exception\MissingTopicEndTimeException;
use App\Repository\TopicRepository;
use DateTime;

class TopicService extends AbstractService
{
    private $topicRepository;

    public function __construct(TopicRepository $topicRepository)
    {
        parent::__construct($topicRepository);
        $this->topicRepository = $topicRepository;
    }

    /**
     * @param Topic $topic
     * @param bool $flush
     * @return void
     */
    public function createTopic(Topic $topic, bool $flush = true): void
    {
        if (null === $topic->getCloseTime()) {
            $topic->setCloseTime((new DateTime())->modify('+1 minute'));
        }

        $this->topicRepository->save($topic, $flush);
    }

    /**
     * @param Topic $topic
     * @param bool $flush
     * @return void
     * @throws MissingTopicEndTimeException
     */
    public function updateTopic(Topic $topic, bool $flush = true): void
    {
        if (null === $topic->getCloseTime()) {
            throw new MissingTopicEndTimeException();
        }

        $this->topicRepository->save($topic, $flush);
    }
}