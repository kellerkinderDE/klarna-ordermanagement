<?php

namespace BestitKlarnaOrderManagement\Components\Serializer;

use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * Extension of the `ObjectNormalizer` class.
 *
 * It transforms empty strings to `null` values as Klarna expects it that way.
 *
 * @author  Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class CustomObjectNormalizer extends AbstractNormalizer
{
    /** @var ObjectNormalizer */
    protected $objectNormalizer;

    public function __construct(ObjectNormalizer $objectNormalizer)
    {
        parent::__construct();

        $this->objectNormalizer = $objectNormalizer;
    }

    /**
     * Denormalizes data back into an object of the given class.
     *
     * @param mixed  $data    data to restore
     * @param string $type    the expected class to instantiate
     * @param string $format  format the given data was extracted from
     * @param array  $context options available to the denormalizer
     */
    public function denormalize($data, $type, $format = null, array $context = []): object
    {
        if (!is_array($data)) {
            return $this->objectNormalizer->denormalize($data, $type, $format, $context);
        }

        return $this->objectNormalizer->denormalize(
            $this->transformEmptyStringsToNull($data),
            $type,
            $format,
            $context
        );
    }

    /**
     * Checks whether the given class is supported for denormalization by this normalizer.
     *
     * @param mixed  $data   Data to denormalize from
     * @param string $type   The class to which the data should be denormalized
     * @param string $format The format being deserialized from
     */
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return $this->objectNormalizer->supportsDenormalization($data, $type, $format);
    }

    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param object $object  object to normalize
     * @param string $format  format the normalization result will be encoded as
     * @param array  $context Context options for the normalizer
     *
     * @return array|scalar
     */
    public function normalize($object, $format = null, array $context = [])
    {
        $normalizedData = $this->objectNormalizer->normalize($object, $format, $context);

        if (!is_array($normalizedData)) {
            return $normalizedData;
        }

        return $this->transformEmptyStringsToNull($normalizedData);
    }

    /**
     * Checks whether the given class is supported for normalization by this normalizer.
     *
     * @param mixed  $data   Data to normalize
     * @param string $format The format being (de-)serialized from or into
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return $this->objectNormalizer->supportsNormalization($data, $format);
    }

    protected function transformEmptyStringsToNull(array $data): array
    {
        /**
         * Make sure that all empty values are being sent as `NULL` to Klarna.
         * That's what they expect.
         */
        foreach ($data as $key => $value) {
            if (is_string($value) && empty($value)) {
                $data[$key] = null;
            }
        }

        return $data;
    }
}
