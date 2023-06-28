<?php
declare(strict_types=1);

namespace Retrofit\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
readonly class HeaderMap
{
    public function __construct(private bool $allowUnsafeNonAsciiValues = false)
    {
    }

    public function allowUnsafeNonAsciiValues(): bool
    {
        return $this->allowUnsafeNonAsciiValues;
    }
}
