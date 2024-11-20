<?php

namespace App\Entity;

use App\Interface\IAudit;
use App\Repository\VoteRepository;
use App\Utils\Enum\SerializerGroups;
use App\Utils\Trait\AuditTrait;
use App\Utils\Trait\UuidTrait;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VoteRepository::class)]
#[ORM\Table(name: 'votes')]
class Vote implements IAudit
{
    use AuditTrait, UuidTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    #[Serializer\Groups([SerializerGroups::DEFAULT])]
    private string $choice;

    #[ORM\ManyToOne(targetEntity: Topic::class, inversedBy: 'votes')]
    #[Assert\NotBlank]
    #[Serializer\MaxDepth(1)]
    #[Serializer\Groups([SerializerGroups::DEPTHS])]
    private Topic $topic;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'votes')]
    #[Assert\NotBlank]
    #[Serializer\MaxDepth(1)]
    #[Serializer\Groups([SerializerGroups::DEPTHS])]
    private User $user;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Vote
    {
        $this->id = $id;
        return $this;
    }

    public function getChoice(): string
    {
        return $this->choice;
    }

    public function setChoice(string $choice): Vote
    {
        $this->choice = $choice;
        return $this;
    }

    public function getTopic(): Topic
    {
        return $this->topic;
    }

    public function setTopic(Topic $topic): Vote
    {
        $this->topic = $topic;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): Vote
    {
        $this->user = $user;
        return $this;
    }
}
