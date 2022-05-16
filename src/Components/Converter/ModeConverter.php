<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Converter;

/**
 * Converters which converts shopware basket item mode to Klarna item type
 *
 * @author Senan Sharhan <senan.sharhan@bestit-online.de>
 */
class ModeConverter
{
    /** @var ModeInterface[] */
    protected $modeConverters;

    /**
     * @param ModeInterface[] $modeConverters
     */
    public function __construct(ModeInterface ...$modeConverters)
    {
        $this->modeConverters = $modeConverters;
    }

    public function convert($mode, $price = null)
    {
        return $this->getConverter($mode)->convert($mode, $price);
    }

    /**
     * Returns the Supported converter
     *
     * @param int $mode
     */
    public function getConverter($mode): ModeInterface
    {
        foreach ($this->modeConverters as $modeConverter) {
            if ($modeConverter->isSupported($mode)) {
                return $modeConverter;
            }
        }

        return new DefaultMode();
    }
}
