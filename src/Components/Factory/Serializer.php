<?php

namespace BestitKlarnaOrderManagement\Components\Factory;

use BestitKlarnaOrderManagement\Components\Serializer\CustomObjectNormalizer;
use BestitKlarnaOrderManagement\Components\Serializer\OptionsNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer as SymfonySerializer;

/**
 * Factory responsible for creating a Serializer.
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class Serializer
{
    public static function create(): SymfonySerializer
    {
        $objectNormalizer = new ObjectNormalizer(
            null,
            new CamelCaseToSnakeCaseNameConverter()
        );

        $customObjectNormalizer = new CustomObjectNormalizer($objectNormalizer);

        /**
         * Because the `ObjectNormalizer` supports all objects, it will try to normalize/denormalize
         * DateTime Objects which will throw an Exception because the DateTime objects constructor
         * requires an argument. In order to fix that the `DateTimeNormalizer` was introduced.
         *
         * Since the `DateTimeNormalizer` only tries to normalize/denormalize `DateTime` objects,
         * it needs to be specified first. It will just ignore all other objects.
         * Internally the serializer loops over all normalizers and grabs the first supported one.
         */
        $normalizers = [
            new DateTimeNormalizer(),
            new ArrayDenormalizer(),
            new OptionsNormalizer(),
            $customObjectNormalizer,
            $objectNormalizer,
        ];
        $encoders = [new JsonEncoder()];

        return new SymfonySerializer($normalizers, $encoders);
    }
}
