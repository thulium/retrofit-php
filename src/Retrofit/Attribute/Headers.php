<?php

namespace Retrofit\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
readonly class Headers
{
    public function __construct(
        public array $headers,
        public bool $allowUnsafeNonAsciiValues = false
    )
    {
    }
}
