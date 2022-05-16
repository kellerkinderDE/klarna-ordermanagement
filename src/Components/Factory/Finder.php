<?php

namespace BestitKlarnaOrderManagement\Components\Factory;

use Symfony\Component\Finder\Finder as SymfonyFinder;

/**
 * Factory responsible for creating a Finder.
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class Finder
{
    public static function create(): SymfonyFinder
    {
        return new SymfonyFinder();
    }
}
