<?php

namespace BestitKlarnaOrderManagement\Components\Calculator;

use BestitKlarnaOrderManagement\Components\Exception\NoSupportedCalculatorException;

/**
 * Factory to choose a calculator, the "bcmath" calculator is prioritized.
 *
 * @package BestitKlarnaOrderManagement\Components\Calculator
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class CalculatorFactory
{
    /**
     * @param CalculatorInterface[] $calculators
     *
     * @return CalculatorInterface
     *
     * @throws NoSupportedCalculatorException
     */
    public static function create(CalculatorInterface ...$calculators)
    {
        foreach ($calculators as $calculator) {
            if ($calculator->isSupported()) {
                return $calculator;
            }
        }

        throw new NoSupportedCalculatorException();
    }
}
