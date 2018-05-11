<?php

namespace BestitKlarnaOrderManagement\Components\Factory;

use Symfony\Component\Finder\Finder as SymfonyFinder;

/**
 * Factory responsible for creating a Finder.
 *
 * @package BestitKlarnaOrderManagement\Components\Factory
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class Finder
{
    /**
     * @return SymfonyFinder
     */
    public static function create()
    {
        return new SymfonyFinder();
    }
}
