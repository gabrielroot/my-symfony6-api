<?php

namespace App\Entity;

use App\Interface\IAudit;
use App\Repository\CooperativeRepository;
use App\Utils\Enum\SerializerGroups;
use App\Utils\Trait\AuditTrait;
use App\Utils\Trait\UuidTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CooperativeRepository::class)]
#[ORM\Table(name: 'cooperatives')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_NAME', fields: ['name'])]
class Cooperative implements IAudit
{
    use AuditTrait, UuidTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank]
    #[Serializer\Groups([SerializerGroups::DEFAULT])]
    private ?string $name = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank]
    #[Serializer\Groups([SerializerGroups::DEFAULT])]
    private ?string $fantasyName = null;

    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'cooperative')]
    #[Serializer\MaxDepth(1)]
    #[Serializer\Groups([SerializerGroups::DEPTHS])]
    private Collection|null $users = null;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Cooperative
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): Cooperative
    {
        $this->name = $name;
        return $this;
    }

    public function getFantasyName(): ?string
    {
        return $this->fantasyName;
    }

    public function setFantasyName(?string $fantasyName): Cooperative
    {
        $this->fantasyName = $fantasyName;
        return $this;
    }

    public function getUsers(): ?Collection
    {
        return $this->users;
    }

    public function setUsers(?Collection $users): Cooperative
    {
        $this->users = $users;
        return $this;
    }
}
