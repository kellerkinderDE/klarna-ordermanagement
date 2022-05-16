<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Calculator;

/**
 * Fallback calculator based on simple PHP functions.
 *
 * This calculator may produce rounding errors.
 * Use the "bcmath" calculator if possible.
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class PhpCalculator implements CalculatorInterface
{
    /**
     * Is the calculation method supported?
     */
    public function isSupported(): bool
    {
        return true;
    }

    /**
     * Converts the amount which was given to the instance to cents.
     *
     * @param float $amount
     */
    public function toCents($amount): int
    {
        /**
         * We need to round here so that values like "-7.6475" get converted correctly.
         * Without the rounding this would first get converted to "-764.75" and then
         * the int cast will convert it to "-764" which is incorrect (it should be "-765").
         */
        $amount = round($amount, 2);

        /**
         * A string is used as a "middle man" to convert a floating point number to an integer.
         * The string is used to avoid PHP problems when casting a floating point number to
         * an integer directly.
         *
         * @see http://php.net/manual/de/language.types.integer.php#language.types.integer.casting.from-float
         *
         * This is a hack and may cause other issues that we're unaware of at this very moment.
         * Please use the "bcmath" calculator if you have that option as it is more reliable.
         */
        $stringifiedNumber = (string) ($amount * 100);

        return (int) $stringifiedNumber;
    }

    /**
     * Converts the amount which was given to the method to a float.
     *
     * @param int $amount
     */
    public function toMajorUnit($amount): float
    {
        return (float) ($amount / 100);
    }
}
