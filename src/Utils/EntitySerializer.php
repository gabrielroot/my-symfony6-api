<?php

namespace App\Utils;

use JMS\Serializer\Expression\ExpressionEvaluator;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class EntitySerializer
{
    public static function builder(): Serializer
    {
        return SerializerBuilder::create()
            ->setPropertyNamingStrategy(new SerializedNameAnnotationStrategy(new IdenticalPropertyNamingStrategy())            )
            ->setExpressionEvaluator(new ExpressionEvaluator(new ExpressionLanguage()))
            ->build();
    }
}