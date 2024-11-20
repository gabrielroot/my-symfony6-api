<?php

namespace App\Entity;

use App\Interface\IAudit;
use App\Repository\TopicRepository;
use App\Repository\UserRepository;
use App\Utils\Enum\SerializerGroups;
use App\Utils\Trait\AuditTrait;
use App\Utils\Trait\UuidTrait;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TopicRepository::class)]
#[ORM\Table(name: 'topics')]
class Topic implements IAudit
{
    use AuditTrait, UuidTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 180)]
    #[Serializer\Groups([SerializerGroups::DEFAULT])]
    private string $title;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Serializer\Groups([SerializerGroups::DEFAULT])]
    private ?string $description = null;

    #[ORM\Column(type: 'datetime')]
    #[Serializer\Groups([SerializerGroups::DEFAULT])]
    private ?DateTime $closeTime;

    #[ORM\ManyToOne(targetEntity: Cooperative::class)]
    #[Assert\NotBlank]
    #[Serializer\MaxDepth(1)]
    #[Serializer\Groups([SerializerGroups::DEPTHS])]
    private Cooperative $cooperative;

    #[ORM\OneToMany(targetEntity: Vote::class, mappedBy: 'topic')]
    #[Serializer\MaxDepth(1)]
    #[Serializer\Groups([SerializerGroups::DEPTHS])]
    private Collection $votes;

    public function __construct()
    {

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Topic
    {
        $this->id = $id;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): Topic
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): Topic
    {
        $this->description = $description;
        return $this;
    }

    public function getCloseTime(): ?DateTime
    {
        return $this->closeTime;
    }

    public function setCloseTime(?DateTime $closeTime): Topic
    {
        $this->closeTime = $closeTime;
        return $this;
    }

    public function getCooperative(): Cooperative
    {
        return $this->cooperative;
    }

    public function setCooperative(Cooperative $cooperative): Topic
    {
        $this->cooperative = $cooperative;
        return $this;
    }

    public function getVotes(): Collection
    {
        return $this->votes;
    }

    public function setVotes(Collection $votes): Topic
    {
        $this->votes = $votes;
        return $this;
    }
}
