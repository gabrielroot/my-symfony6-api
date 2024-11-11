<?php

namespace App\Utils\Trait;

use App\Utils\EntitySerializer;
use App\Utils\Enum\SerializerGroups;
use JMS\Serializer\SerializationContext;

trait SerializationTrait
{
    public function toArray(array $serializerGroups = []): array
    {
        $context = SerializationContext::create()
            ->enableMaxDepthChecks()
            ->setSerializeNull(true)
            ->setGroups(array_merge([SerializerGroups::DEFAULT, SerializerGroups::AUDIT], $serializerGroups))
        ;

        return EntitySerializer::builder()->toArray($this, $context);
    }
}