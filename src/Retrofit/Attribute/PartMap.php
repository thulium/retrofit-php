<?php

namespace Retrofit\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
readonly class PartMap
{
    public function __construct(public string $encoding = 'binary')
    {
    }
}
