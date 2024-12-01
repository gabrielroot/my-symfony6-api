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
#[ORM\Table(name: 'addresses')]
class Address implements IAudit
{
    use AuditTrait, UuidTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    #[Assert\Length(max: 200)]
    #[Serializer\Groups([SerializerGroups::DEFAULT])]
    private string $street;

    #[ORM\Column(type: 'string')]
    #[Assert\Length(max: 2)]
    #[Serializer\Groups([SerializerGroups::DEFAULT])]
    private string $uf;

    #[ORM\Column(type: 'string')]
    #[Assert\Length(max: 50)]
    #[Serializer\Groups([SerializerGroups::DEFAULT])]
    private string $state;

    #[ORM\Column(type: 'string')]
    #[Assert\Length(max: 50)]
    #[Serializer\Groups([SerializerGroups::DEFAULT])]
    private string $city;

    #[ORM\Column(type: 'string')]
    #[Assert\Length(max: 10)]
    #[Serializer\Groups([SerializerGroups::DEFAULT])]
    private string $number;

    #[ORM\Column(type: 'string')]
    #[Assert\Length(max: 200)]
    #[Serializer\Groups([SerializerGroups::DEFAULT])]
    private string $complement;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank]
    #[Assert\Regex('^[0-9]+$')]
    #[Assert\Length(exactly: 8)]
    #[Serializer\Groups([SerializerGroups::DEFAULT])]
    private string $zipCode;

    #[ORM\OneToOne(targetEntity: Cooperative::class, mappedBy: 'address')]
    #[Assert\NotBlank]
    #[Serializer\MaxDepth(1)]
    #[Serializer\Groups([SerializerGroups::DEPTHS])]
    private Cooperative $cooperative;

    #[ORM\OneToOne(targetEntity: User::class, mappedBy: 'address')]
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

    public function setId(?int $id): Address
    {
        $this->id = $id;
        return $this;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    public function setStreet(string $street): Address
    {
        $this->street = $street;
        return $this;
    }

    public function getUf(): string
    {
        return $this->uf;
    }

    public function setUf(string $uf): Address
    {
        $this->uf = $uf;
        return $this;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): Address
    {
        $this->state = $state;
        return $this;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): Address
    {
        $this->city = $city;
        return $this;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function setNumber(string $number): Address
    {
        $this->number = $number;
        return $this;
    }

    public function getComplement(): string
    {
        return $this->complement;
    }

    public function setComplement(string $complement): Address
    {
        $this->complement = $complement;
        return $this;
    }

    public function getZipCode(): string
    {
        return $this->zipCode;
    }

    public function setZipCode(string $zipCode): Address
    {
        $this->zipCode = $zipCode;
        return $this;
    }

    public function getTopic(): Topic
    {
        return $this->topic;
    }

    public function setTopic(Topic $topic): Address
    {
        $this->topic = $topic;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): Address
    {
        $this->user = $user;
        return $this;
    }
}
