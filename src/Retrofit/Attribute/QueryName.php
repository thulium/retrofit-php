<?php

namespace Retrofit\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
readonly class QueryName
{
    public function __construct(
        public bool $encoded = false
    )
    {
    }
}
