<?php
declare(strict_types=1);

namespace Retrofit\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
readonly class Header
{
    public function __construct(
        private string $name,
        private bool $allowUnsafeNonAsciiValues = false
    )
    {
    }

    public function name(): string
    {
        return $this->name;
    }

    public function allowUnsafeNonAsciiValues(): bool
    {
        return $this->allowUnsafeNonAsciiValues;
    }
}
