<?php

declare(strict_types=1);

namespace BestitKlarnaOrderManagement\Components\Calculator;

/**
 * Defines the required methods for a calculator.
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
interface CalculatorInterface
{
    /**
     * Is the calculation method supported?
     */
    public function isSupported(): bool;

    /**
     * Converts the amount which was given to the method to cents.
     *
     * @param float $amount
     */
    public function toCents($amount): int;

    /**
     * Converts the amount which was given to the major unit of a currency.
     *
     * @param int $amount
     */
    public function toMajorUnit($amount): float;
}
