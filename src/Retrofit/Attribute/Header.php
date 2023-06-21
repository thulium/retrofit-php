<?php

namespace Retrofit\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
readonly class Header
{
    public function __construct(
        public string $name,
        public bool $allowUnsafeNonAsciiValues = false
    )
    {
    }
}
