<?php

namespace BestitKlarnaOrderManagement\Components\Serializer;

use BestitKlarnaOrderManagement\Components\Api\Model\Options;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Normalizes a {@link Options} to the format that Klarna expects.
 *
 * @package BestitKlarnaOrderManagement\Components\Serializer
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class OptionsNormalizer implements NormalizerInterface
{
    /**
     * Normalizes an object into a set of arrays/scalars.
     *
     * @param Options $options  object to normalize
     * @param string $format  format the normalization result will be encoded as
     * @param array  $context Context options for the normalizer
     *
     * @return array
     */
    public function normalize($options, $format = null, array $context = [])
    {
        return [
            'color_button' => $this->normalizeValue($options->colorButton),
            'color_button_text' => $this->normalizeValue($options->colorButtonText),
            'color_checkbox' => $this->normalizeValue($options->colorCheckbox),
            'color_checkbox_checkmark' => $this->normalizeValue($options->colorCheckboxCheckmark),
            'color_header' => $this->normalizeValue($options->colorHeader),
            'color_link' => $this->normalizeValue($options->colorLink),
            'color_border' => $this->normalizeValue($options->colorBorder),
            'color_border_selected' => $this->normalizeValue($options->colorBorderSelected),
            'color_text' => $this->normalizeValue($options->colorText),
            'color_details' => $this->normalizeValue($options->colorDetails),
            'color_text_secondary' => $this->normalizeValue($options->colorTextSecondary),
            'radius_border' => $this->normalizeValue($options->radiusBorder),
        ];
    }

    /**
     * Checks whether the given class is supported for normalization by this normalizer.
     *
     * @param mixed  $data   Data to normalize
     * @param string $format The format being (de-)serialized from or into
     *
     * @return bool
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Options;
    }

    /**
     * @param mixed $value
     *
     * @return mixed|null
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
