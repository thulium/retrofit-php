<?php
declare(strict_types=1);

namespace Retrofit\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
readonly class PartMap
{
    public function __construct(private string $encoding = 'binary')
    {
    }

    public function encoding(): string
    {
        return $this->encoding;
    }
}
