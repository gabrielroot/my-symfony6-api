<?php

namespace App\Service;

use App\Entity\Topic;
use App\Entity\Vote;
use App\Exception\MemberAlreadyVotedException;
use App\Exception\SessionClosedToVoteException;
use App\Exception\TopicNotFromMemberCooperativeException;
use App\Repository\VoteRepository;
use App\Utils\Enum\VoteChoice;
use DateInterval;
use DateTime;

class VoteService extends AbstractService
{
    private $voteRepository;

    public function __construct(VoteRepository $voteRepository)
    {
        parent::__construct($voteRepository);
        $this->voteRepository = $voteRepository;
    }

    /**
     * @param Vote $vote
     * @param bool $flush
     * @return void
     */
    public function createVote(Vote $vote, bool $flush = true): void
    {
        $hasVotedAlready = $this->findBy(['topic' => $vote->getTopic(), 'user' => $vote->getUser()]);
        if ($hasVotedAlready) {
            throw new MemberAlreadyVotedException();
        }

        $isSessionClosed = $vote->getTopic()->getCloseTime() < new DateTime();
        if ($isSessionClosed) {
            throw new SessionClosedToVoteException();
        }

        $isTopicFromMemberCooperative = $vote->getTopic()->getCooperative() === $vote->getUser()->getCooperative();
        if (!$isTopicFromMemberCooperative) {
            throw new TopicNotFromMemberCooperativeException();
        }

        $this->voteRepository->save($vote, $flush);
    }

    public function countVotes(Topic $topic): array
    {
        $votesOnTopic = $this->findBy(['topic' => $topic]);

        $positiveVotes = array_filter($votesOnTopic, function (Vote $vote){
            return $vote->getChoice() === VoteChoice::POSITIVE;
        });

        $negativeVotes = array_filter($votesOnTopic, function (Vote $vote){
            return $vote->getChoice() === VoteChoice::NEGATIVE;
        });

        return [
            'positive' => [
                'total' => count($positiveVotes),
                'votes' => [...$positiveVotes],
            ],
            'negative' => [
                'total' => count($negativeVotes),
                'votes' => [...$negativeVotes],
            ]
        ];
    }

}