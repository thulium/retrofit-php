<?php

namespace Retrofit\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
readonly class FieldMap
{
    public function __construct(public bool $encoded = false)
    {
    }
}
