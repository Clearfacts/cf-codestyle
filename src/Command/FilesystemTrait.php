<?php

declare(strict_types=1);

namespace Clearfacts\Bundle\CodestyleBundle\Command;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

trait FilesystemTrait
{
    public function getFileSystem(): Filesystem
    {
        return new Filesystem();
    }

    public function getFinder(): Finder
    {
        return new Finder();
    }
}
