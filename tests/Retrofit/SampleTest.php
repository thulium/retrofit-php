<?php

namespace Retrofit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SampleTest extends TestCase
{
    #[Test]
    public function shouldName(): void
    {
        $this->assertTrue(true);
    }
}
