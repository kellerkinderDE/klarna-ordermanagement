<?php

namespace BestitKlarnaOrderManagement\Components\Calculator;

/**
 * Calculator using the bcmath extension to reliably work with floats.
 *
 * @package BestitKlarnaOrderManagement\Components\Calculator
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class BcMathCalculator implements CalculatorInterface
{
    /**
     * Is the calculation method supported?
     *
     * @return bool
     */
    public function isSupported()
    {
        return extension_loaded('bcmath');
    }

    /**
     * Converts the amount which was given to the instance to cents.
     *
     * @param float $amount
     *
     * @return int
     */
    public function toCents($amount)
    {
        /**
         * We need to round here so that values like "-7.6475" get converted correctly.
         * Without the rounding this would first get converted to "-764.75" and then
         * the int cast will convert it to "-764" which is incorrect (it should be "-765").
         */
        $amount = round($amount, 2);

        return (int) bcmul($amount, 100);
    }

    /**
     * Converts the amount which was given to the method to a float.
     *
     * @param int $amount
     *
     * @return float
     */
    public function toMajorUnit($amount)
    {
        return (float) bcdiv($amount, 100, 2);
    }
}
