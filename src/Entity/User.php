<?php

namespace App\Entity;

use App\Interface\IAudit;
use App\Repository\UserRepository;
use App\Utils\Enum\SerializerGroups;
use App\Utils\Trait\AuditTrait;
use App\Utils\Trait\UuidTrait;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`users`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
class User implements IAudit
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
    private ?string $name = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 180)]
    #[Serializer\Groups([SerializerGroups::DEFAULT])]
    private ?string $username = null;

    #[ORM\ManyToOne(targetEntity: Cooperative::class)]
    #[Assert\NotBlank]
    #[Serializer\MaxDepth(1)]
    #[Serializer\Groups([SerializerGroups::DEPTHS])]
    private ?Cooperative $cooperative = null;

    #[ORM\OneToMany(targetEntity: Vote::class, mappedBy: 'user')]
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): User
    {
        $this->name = $name;
        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    public function getCooperative(): ?Cooperative
    {
        return $this->cooperative;
    }

    public function setCooperative(?Cooperative $cooperative): User
    {
        $this->cooperative = $cooperative;
        return $this;
    }

    public function getVotes(): Collection
    {
        return $this->votes;
    }

    public function setVotes(Collection $votes): User
    {
        $this->votes = $votes;
        return $this;
    }
}
