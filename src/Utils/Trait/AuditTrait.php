<?php

namespace App\Utils\Trait;

use App\Utils\Enum\SerializerGroups;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

trait AuditTrait
{
    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    #[Serializer\Groups([SerializerGroups::AUDIT])]
    protected DateTime $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Serializer\Groups([SerializerGroups::AUDIT])]
    protected ?DateTime $updatedAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Serializer\Groups([SerializerGroups::AUDIT])]
    protected ?DateTime $deletedAt = null;

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getDeletedAt(): ?DateTime
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?DateTime $deletedAt): self
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }

    #[Serializer\VirtualProperty]
    #[Serializer\SerializedName('active')]
    #[Serializer\Groups([SerializerGroups::DEFAULT])]
    public function isActive(): bool
    {
        return is_null($this->deletedAt);
    }
}