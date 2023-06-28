<?php
declare(strict_types=1);

namespace Retrofit\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
readonly class Headers
{
    public function __construct(
        private array $headers,
        private bool $allowUnsafeNonAsciiValues = false
    )
    {
    }

    public function headers(): array
    {
        return $this->headers;
    }

    public function allowUnsafeNonAsciiValues(): bool
    {
        return $this->allowUnsafeNonAsciiValues;
    }
}
