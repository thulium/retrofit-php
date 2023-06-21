<?php

namespace Retrofit\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
readonly class POST
{
    public function __construct(public string $path)
    {
    }
}
