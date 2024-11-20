<?php

namespace App\Utils\Trait;

use App\Utils\Enum\SerializerGroups;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

trait UuidTrait
{
    #[ORM\Column(type: 'string', unique: true)]
    #[Serializer\Groups([SerializerGroups::DEFAULT])]
    protected string $uuid;

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): void
    {
        $this->uuid = $uuid;
    }
}