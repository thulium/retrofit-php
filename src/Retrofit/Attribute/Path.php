<?php

namespace Retrofit\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
readonly class Path
{
    public function __construct(
        public string $name,
        public bool $encoded = false
    )
    {
    }
}
