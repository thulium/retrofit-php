<?php

declare(strict_types=1);

namespace Retrofit\Core\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
readonly class FieldMap
{
    public function __construct(private bool $encoded = false)
    {
    }

    public function encoded(): bool
    {
        return $this->encoded;
    }
}
