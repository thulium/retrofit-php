<?php

declare(strict_types=1);

namespace Retrofit\Tests;

use Ouzo\Utilities\Path;

trait WithFixtureFile
{
    protected function getFilePath(string $filename): string
    {
        $testsDir = dirname(__FILE__);
        return Path::join($testsDir, 'Fixtures', 'file', $filename);
    }

    protected function getFileResource(string $filename)
    {
        return fopen($this->getFilePath($filename), 'r');
    }
}
