<?php

namespace Retrofit\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
readonly class DELETE
{
    public function __construct(public string $path)
    {
    }
}
