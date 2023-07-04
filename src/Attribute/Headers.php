<?php
declare(strict_types=1);

namespace Retrofit\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
readonly class Headers
{
    public function __construct(private array $value)
    {
    }

    public function value(): array
    {
        return $this->value;
    }
}
