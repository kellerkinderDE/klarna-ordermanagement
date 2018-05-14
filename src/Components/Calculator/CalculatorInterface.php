<?php

namespace BestitKlarnaOrderManagement\Components\Calculator;

/**
 * Defines the required methods for a calculator.
 *
 * @package BestitKlarnaOrderManagement\Components\Calculator
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
interface CalculatorInterface
{
    /**
     * Is the calculation method supported?
     *
     * @return bool
     */
    public function isSupported();

    /**
     * Converts the amount which was given to the method to cents.
     *
     * @param float $amount
     *
     * @return int
     */
    public function toCents($amount);

    /**
     * Converts the amount which was given to the major unit of a currency.
     *
     * @param int $amount
     *
     * @return float
     */
    public function toMajorUnit($amount);
}
