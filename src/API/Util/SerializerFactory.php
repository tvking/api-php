<?php

namespace GroupByInc\API\Util;

use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\Serializer;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;

class SerializerFactory
{

    /**
     * @return Serializer
     */
    public static function build()
    {
        return SerializerBuilder::create()
            ->setPropertyNamingStrategy(new SerializedNameAnnotationStrategy(new IdenticalPropertyNamingStrategy()))
            ->build();
    }
}