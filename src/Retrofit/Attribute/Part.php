<?php

namespace Retrofit\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
readonly class Part
{
    public function __construct(
        public string $name,
        public string $encoding = 'binary'
    )
    {
    }
}
