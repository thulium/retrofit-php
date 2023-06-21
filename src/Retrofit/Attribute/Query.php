<?php

namespace Retrofit\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
readonly class Query
{
    public function __construct(
        public string $name,
        public bool $encoded = false
    )
    {
    }
}
