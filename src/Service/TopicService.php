<?php

namespace App\Service;

use App\Entity\Topic;
use App\Exception\MissingTopicEndTimeException;
use App\Repository\TopicRepository;

class TopicService extends AbstractService
{
    private $topicRepository;

    public function __construct(TopicRepository $topicRepository)
    {
        parent::__construct($topicRepository);
        $this->topicRepository = $topicRepository;
    }

    /**
     * @param Topic $entity
     * @param bool $flush
     * @return void
     * @throws MissingTopicEndTimeException
     */
    public function updateTopic(Topic $entity, bool $flush = true): void
    {
        if (null === $entity->getCloseTime()) {
            throw new MissingTopicEndTimeException();
        }

        $this->topicRepository->save($entity, $flush);
    }
}