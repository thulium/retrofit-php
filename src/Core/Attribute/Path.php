<?php
declare(strict_types=1);

namespace Retrofit\Core\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
readonly class Path
{
    public function __construct(
        private string $name,
        private bool $encoded = false
    )
    {
    }

    public function name(): string
    {
        return $this->name;
    }

    public function encoded(): bool
    {
        return $this->encoded;
    }
}
