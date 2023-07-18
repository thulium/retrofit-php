<?php

declare(strict_types=1);

namespace Retrofit\Core\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
readonly class Headers
{
    public function __construct(
        /**
         * @var array<string, string> $value
         */
        private array $value,
    )
    {
    }

    /**
     * @return array<string, string>
     */
    public function value(): array
    {
        return $this->value;
    }
}
