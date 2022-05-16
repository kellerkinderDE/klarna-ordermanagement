<?php

namespace BestitKlarnaOrderManagement\Components\Serializer;

use BestitKlarnaOrderManagement\Components\Api\Model\Options;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Normalizes a {@link Options} to the format that Klarna expects.
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class OptionsNormalizer implements NormalizerInterface
{
    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param Options $object  object to normalize
     * @param string  $format  format the normalization result will be encoded as
     * @param array   $context Context options for the normalizer
     */
    public function normalize($object, $format = null, array $context = []): array
    {
        return [
            'color_button'             => $this->normalizeValue($object->colorButton),
            'color_button_text'        => $this->normalizeValue($object->colorButtonText),
            'color_checkbox'           => $this->normalizeValue($object->colorCheckbox),
            'color_checkbox_checkmark' => $this->normalizeValue($object->colorCheckboxCheckmark),
            'color_header'             => $this->normalizeValue($object->colorHeader),
            'color_link'               => $this->normalizeValue($object->colorLink),
            'color_border'             => $this->normalizeValue($object->colorBorder),
            'color_border_selected'    => $this->normalizeValue($object->colorBorderSelected),
            'color_text'               => $this->normalizeValue($object->colorText),
            'color_details'            => $this->normalizeValue($object->colorDetails),
            'color_text_secondary'     => $this->normalizeValue($object->colorTextSecondary),
            'radius_border'            => $this->normalizeValue($object->radiusBorder),
        ];
    }

    /**
     * Checks whether the given class is supported for normalization by this normalizer.
     *
     * @param mixed  $data   Data to normalize
     * @param string $format The format being (de-)serialized from or into
     */
    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof Options;
    }

    /**
     * @return null|mixed
     */
    protected function normalizeValue($value)
    {
        /**
         * The shopware configuration for color fields returns "#" if it is empty.
         * Klarna only accepts empty values as NULL.
         */
        if ($value === '#' || empty($value)) {
            return null;
        }

        return $value;
    }
}
