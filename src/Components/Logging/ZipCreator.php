<?php

namespace BestitKlarnaOrderManagement\Components\Logging;

use Symfony\Component\Finder\Finder;
use ZipArchive;

/**
 * Zips our logs files.
 *
 * @package BestitKlarnaOrderManagement\Components\Logging
 *
 * @author Ahmad El-Bardan <ahmad.el-bardan@bestit-online.de>
 */
class ZipCreator
{
    /** @var Finder */
    protected $finder;
    /** @var string */
    protected $logDir;

    /**
     * @param Finder $finder
     * @param string $logDir
     */
    public function __construct(Finder $finder, $logDir)
    {
        $this->finder = $finder;
        $this->logDir = $logDir;
    }

    /**
     * @param string $zipFileName
     *
     * @return string
     */
    public function zipKlarnaLogFiles($zipFileName)
    {
        $this->finder->in($this->logDir)->name('bestit_klarna_*');

        $zip = new ZipArchive();
        $zip->open($zipFileName, ZipArchive::CREATE);

        foreach ($this->finder->files() as $file) {
            $zip->addFromString($file->getBasename(), $file->getContents());
        }

        $zip->close();

        return $zipFileName;
    }
}
