<?php

declare(strict_types=1);

namespace Retrofit\Core\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
readonly class Header
{
    public function __construct(private string $name)
    {
    }

    public function name(): string
    {
        return $this->name;
    }
}
